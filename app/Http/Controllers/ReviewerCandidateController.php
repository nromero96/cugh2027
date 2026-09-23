<?php

namespace App\Http\Controllers;

use App\Mail\ReviewerAccountCreated;
use App\Models\ReviewerCandidate;
use App\Models\User;
use App\Services\ReviewerCandidateImportService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReviewerCandidateController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeManagement();
        $search = trim((string) $request->input('search', ''));

        $reviewers = ReviewerCandidate::query()
            ->with('registeredUser:id,email')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('institution', 'like', '%'.$search.'%')
                        ->orWhere('country', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(30)
            ->withQueryString();

        return view('pages.reviewer_candidates.index', [
            'category_name' => 'reviewer_candidates',
            'page_name' => 'reviewer_candidates',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
            'reviewers' => $reviewers,
            'search' => $search,
            'totalReviewers' => ReviewerCandidate::count(),
            'countriesCount' => ReviewerCandidate::whereNotNull('country')->distinct('country')->count('country'),
            'institutionsCount' => ReviewerCandidate::whereNotNull('institution')->distinct('institution')->count('institution'),
        ]);
    }

    public function import(Request $request, ReviewerCandidateImportService $importer)
    {
        $this->authorizeManagement();
        $request->validate([
            'reviewer_file' => ['required', 'file', 'max:10240', 'mimes:xlsx,xls,csv'],
        ], [
            'reviewer_file.required' => 'Select an Excel or CSV file to import.',
            'reviewer_file.mimes' => 'The file must be XLSX, XLS or CSV.',
            'reviewer_file.max' => 'The file may not be larger than 10 MB.',
        ]);

        try {
            $result = $importer->import($request->file('reviewer_file'));
        } catch (DomainException $exception) {
            return back()->withErrors(['reviewer_file' => $exception->getMessage()]);
        } catch (\Throwable $exception) {
            report($exception);
            return back()->withErrors(['reviewer_file' => 'The file could not be imported. Verify that it is a valid, readable spreadsheet.']);
        }

        $this->removePreviousRejectedReport($request);
        if (! empty($result['rejected_rows'])) {
            $token = (string) Str::uuid();
            $path = 'reviewer-import-errors/'.$token.'.csv';
            Storage::disk('local')->put($path, $importer->rejectedRowsCsv($result['rejected_rows']));
            $request->session()->put('reviewer_import_report', [
                'token' => $token,
                'path' => $path,
                'count' => count($result['rejected_rows']),
            ]);
        }

        return redirect()->route('reviewer_candidates.index')
            ->with('success', $result['created'].' reviewers created, '.$result['updated'].' updated and '.$result['skipped'].' skipped.')
            ->with('import_errors', $result['errors']);
    }

    public function downloadImportErrors(Request $request, $token)
    {
        $this->authorizeManagement();
        $report = $request->session()->get('reviewer_import_report');

        abort_unless(
            is_array($report)
            && hash_equals((string) ($report['token'] ?? ''), (string) $token)
            && Storage::disk('local')->exists($report['path'] ?? ''),
            404
        );

        $absolutePath = Storage::disk('local')->path($report['path']);
        $request->session()->forget('reviewer_import_report');

        return response()->download(
            $absolutePath,
            'reviewer-import-errors-'.now()->format('Y-m-d-His').'.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8']
        )->deleteFileAfterSend(true);
    }

    public function createUser(ReviewerCandidate $reviewerCandidate)
    {
        $this->authorizeManagement();

        try {
            $created = DB::transaction(function () use ($reviewerCandidate) {
                $candidate = ReviewerCandidate::query()->lockForUpdate()->findOrFail($reviewerCandidate->id);

                if (User::where('email', $candidate->email)->exists()) {
                    return false;
                }

                $plainPassword = Str::random(16);
                $user = User::create([
                    'salutation' => $candidate->salutation,
                    'name' => $candidate->first_name ?: 'Reviewer',
                    'lastname' => $candidate->last_name,
                    'job_title' => $candidate->professional_position_academic_title,
                    'workplace' => $candidate->institution,
                    'document_type' => 'Other',
                    'document_number' => 'REVIEWER-'.$candidate->id.'-'.Str::upper(Str::random(8)),
                    'email' => Str::lower(trim($candidate->email)),
                    'password' => Hash::make($plainPassword),
                    'status' => 'active',
                    'photo' => 'default-profile.jpg',
                ]);

                $user->assignRole('Participante');
                $user->inscription()->create([
                    'invoice_type' => 'Boleta',
                    'status' => 'Draft',
                ]);

                $mail = Mail::to($user->email);
                $notificationCopy = config('services.correonotificacion.copy');
                if ($notificationCopy) {
                    $mail->bcc($notificationCopy);
                }
                $mail->send(new ReviewerAccountCreated($user, $plainPassword));

                return true;
            });
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'The account could not be created or the credentials email could not be sent. No user was created.');
        }

        if (!$created) {
            return back()->with('error', 'A user with this email address already exists.');
        }

        return back()->with('success', 'The participant account was created and the login credentials were emailed successfully.');
    }

    public function template()
    {
        $this->authorizeManagement();
        $headers = array_values(ReviewerCandidateImportService::HEADERS);

        return new StreamedResponse(function () use ($headers) {
            $stream = fopen('php://output', 'w');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, $headers);
            fclose($stream);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="reviewer-import-template.csv"',
        ]);
    }

    private function authorizeManagement()
    {
        $user = auth()->user();
        abort_unless($user && ($user->hasRole('Administrador') || $user->hasRole('Secretaria')), 403);
    }

    private function removePreviousRejectedReport(Request $request)
    {
        $previous = $request->session()->pull('reviewer_import_report');
        if (is_array($previous) && ! empty($previous['path'])) {
            Storage::disk('local')->delete($previous['path']);
        }
    }
}
