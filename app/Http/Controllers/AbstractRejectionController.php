<?php

namespace App\Http\Controllers;

use App\Mail\AbstractRejection;
use App\Models\AbstractPost;
use App\Models\AbstractPostEmail;
use App\Models\AbstractPostNote;
use App\Services\AbstractRejectionLetterPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AbstractRejectionController extends Controller
{
    private const ELIGIBLE_STATUSES = ['draft', 'submitted', 'qualified'];
    private const SALUTATIONS = ['Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Prof.'];

    public function index(Request $request)
    {
        $this->ensureAdministrator();

        $request->validate(['search' => ['nullable', 'string', 'max:255']]);
        $search = trim((string) $request->query('search', ''));

        $query = AbstractPost::with(['user', 'emails' => function ($emails) {
            $emails->where('kind', 'rejection')->where('status', 'sent');
        }]);

        if ($search === '') {
            $query->whereIn('status', self::ELIGIBLE_STATUSES);
        } else {
            $query->where(function ($abstracts) {
                $abstracts->whereIn('status', self::ELIGIBLE_STATUSES)
                    ->orWhereHas('emails', function ($emails) {
                        $emails->where('kind', 'rejection')->where('status', 'sent');
                    });
            });

            $idSearch = ltrim($search, '#');
            $query->where(function ($abstracts) use ($search, $idSearch) {
                if (ctype_digit($idSearch)) {
                    $abstracts->whereKey((int) $idSearch);
                } else {
                    $abstracts->whereHas('user', function ($users) use ($search) {
                        $users->where('email', 'like', '%'.$search.'%');
                    })->orWhereHas('emails', function ($emails) use ($search) {
                        $emails->where('kind', 'rejection')->where('status', 'sent')
                            ->where('recipient_email', 'like', '%'.$search.'%');
                    });
                }
            });
        }

        $abstracts = $query->orderBy('id')->paginate(20)->appends(['search' => $search]);

        return view('pages.abstract_posts.rejection-emails', [
            'abstracts' => $abstracts,
            'search' => $search,
            'category_name' => 'abstract_posts',
            'page_name' => 'abstract_posts',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ]);
    }

    public function preview(AbstractPost $abstractPost)
    {
        $this->ensureAdministrator();
        abort_unless(in_array($abstractPost->status, self::ELIGIBLE_STATUSES, true), 404);

        return response()->view('emails.abstract-rejection', $this->letterData($abstractPost->load('user')));
    }

    public function previewLetter(AbstractPost $abstractPost, AbstractRejectionLetterPdf $letterPdf)
    {
        $this->ensureAdministrator();
        abort_unless(in_array($abstractPost->status, self::ELIGIBLE_STATUSES, true), 404);

        $data = $this->letterData($abstractPost->load('user'));
        return response($letterPdf->render($data, $abstractPost->id))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$this->attachmentFilename($abstractPost->id).'"');
    }

    public function archive(AbstractPostEmail $email)
    {
        $this->ensureAdministrator();
        abort_unless($email->status === 'sent', 404);

        return response($email->body_html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Security-Policy', 'sandbox')
            ->header('X-Content-Type-Options', 'nosniff');
    }

    public function downloadAttachment(AbstractPostEmail $email)
    {
        $this->ensureAdministrator();
        abort_unless($email->status === 'sent' && $email->attachment_disk === 'local'
            && $email->attachment_path && Storage::disk('local')->exists($email->attachment_path), 404);

        return Storage::disk('local')->download($email->attachment_path, $email->attachment_filename);
    }

    public function send(AbstractPost $abstractPost, AbstractRejectionLetterPdf $letterPdf)
    {
        $this->ensureAdministrator();
        $mailSent = false;
        $attachmentPath = null;

        try {
            DB::transaction(function () use ($abstractPost, $letterPdf, &$mailSent, &$attachmentPath) {
                $locked = AbstractPost::with('user')->whereKey($abstractPost->id)->lockForUpdate()->firstOrFail();
                if (!in_array($locked->status, self::ELIGIBLE_STATUSES, true)) {
                    throw ValidationException::withMessages(['abstract' => 'This abstract is no longer eligible for a rejection email.']);
                }

                $previous = AbstractPostEmail::where('abstract_post_id', $locked->id)
                    ->where('kind', 'rejection')
                    ->where('status', 'sent')
                    ->first();
                if ($previous) {
                    throw ValidationException::withMessages(['abstract' => 'A rejection email has already been sent for this abstract.']);
                }

                $data = $this->letterData($locked);
                $bodyHtml = view('emails.abstract-rejection', $data)->render();
                $subject = $this->subjectLine($locked, $data['presenterName']);
                $attachmentFilename = $this->attachmentFilename($locked->id);
                $attachmentPath = 'abstract-post-emails/rejection/'.$locked->id.'/'.Str::uuid().'.pdf';
                $pdfBytes = $letterPdf->render($data, $locked->id);
                if (!Storage::disk('local')->put($attachmentPath, $pdfBytes)) {
                    throw new \RuntimeException('Could not save the rejection letter PDF.');
                }
                $previousStatus = $locked->status;

                Mail::to($locked->user->email)->send(new AbstractRejection(
                    $subject,
                    $bodyHtml,
                    Storage::disk('local')->path($attachmentPath),
                    $attachmentFilename
                ));
                $mailSent = true;

                AbstractPostEmail::create([
                    'abstract_post_id' => $locked->id,
                    'sent_by_user_id' => auth()->id(),
                    'kind' => 'rejection',
                    'recipient_email' => $locked->user->email,
                    'from_email' => config('mail.from.address'),
                    'reply_to_email' => AbstractRejection::REPLY_TO,
                    'subject' => $subject,
                    'body_html' => $bodyHtml,
                    'attachment_disk' => 'local',
                    'attachment_path' => $attachmentPath,
                    'attachment_filename' => $attachmentFilename,
                    'attachment_mime' => 'application/pdf',
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $locked->status = 'not_accepted';
                $locked->save();

                AbstractPostNote::create([
                    'abstract_post_id' => $locked->id,
                    'user_id' => auth()->id(),
                    'comment' => 'Rejection email sent to '.$locked->user->email.'.',
                    'status_change' => 'Changed status from '.$previousStatus.' to not_accepted',
                ]);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            if (!$mailSent && $attachmentPath) {
                Storage::disk('local')->delete($attachmentPath);
            }
            report($exception);

            return back()->with('error', $mailSent
                ? 'The email may have been sent, but its database record could not be completed. Do not resend until an administrator verifies delivery.'
                : 'The rejection email could not be sent. The abstract status was not changed.');
        }

        return redirect()->route('abstract_posts.rejection_emails.index')
            ->with('success', 'The rejection email was sent and recorded.');
    }

    private function letterData(AbstractPost $abstractPost): array
    {
        $user = $abstractPost->user;
        $name = $user ? trim(preg_replace('/\s+/u', ' ', trim($user->name.' '.$user->lastname.' '.$user->second_lastname))) : '';
        $salutation = $user && in_array($user->salutation, self::SALUTATIONS, true) ? $user->salutation : '';
        $title = trim((string) $abstractPost->title);
        $presenterName = $abstractPost->presenterName();

        if (!$user || !filter_var($user->email, FILTER_VALIDATE_EMAIL) || $title === '') {
            throw ValidationException::withMessages([
                'abstract' => 'The recipient must have a valid email, and the abstract must have a title before sending.',
            ]);
        }

        return [
            'salutation' => $salutation,
            'recipientName' => $name,
            'abstractTitle' => $title,
            'presenterName' => $presenterName,
            'letterDate' => now('America/Lima')->format('F j, Y'),
        ];
    }

    private function subjectLine(AbstractPost $abstractPost, string $presenterName): string
    {
        $subject = 'NOTIFICATION OF ABSTRACT DECISION N° '.$abstractPost->id;

        return $presenterName !== '' ? $subject.' - '.$presenterName : $subject;
    }

    private function attachmentFilename(int $abstractId): string
    {
        return 'Abstract-Rejection-'.sprintf('%06d', $abstractId).'.pdf';
    }

    private function ensureAdministrator(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('Administrador'), 403);
    }
}
