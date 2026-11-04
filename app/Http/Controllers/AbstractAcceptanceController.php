<?php

namespace App\Http\Controllers;

use App\Mail\AbstractAcceptance;
use App\Models\AbstractPost;
use App\Models\AbstractPostEmail;
use App\Services\AbstractAcceptanceLetterPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AbstractAcceptanceController extends Controller
{
    private const PRESENTATION_TYPES = ['Oral Presentation', 'Poster'];

    public function index(Request $request)
    {
        $this->ensureAdministrator();
        $request->validate(['search' => ['nullable', 'string', 'max:255']]);
        $search = trim((string) $request->query('search', ''));

        $query = AbstractPost::with(['user', 'emails' => function ($emails) {
            $emails->where('kind', 'acceptance')->where('status', 'sent');
        }])->where('status', 'accepted');

        if ($search !== '') {
            $idSearch = ltrim($search, '#');
            $query->where(function ($abstracts) use ($search, $idSearch) {
                if (ctype_digit($idSearch)) {
                    $abstracts->whereKey((int) $idSearch);
                } else {
                    $abstracts->whereHas('user', function ($users) use ($search) {
                        $users->where('email', 'like', '%'.$search.'%');
                    })->orWhereHas('emails', function ($emails) use ($search) {
                        $emails->where('kind', 'acceptance')->where('status', 'sent')
                            ->where('recipient_email', 'like', '%'.$search.'%');
                    });
                }
            });
        }

        $abstracts = $query->orderBy('id')->paginate(20)->appends(['search' => $search]);

        return view('pages.abstract_posts.acceptance-emails', [
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
        $this->ensureEligible($abstractPost);

        return response()->view('emails.abstract-acceptance', $this->letterData($abstractPost->load('user')));
    }

    public function previewLetter(AbstractPost $abstractPost, AbstractAcceptanceLetterPdf $letterPdf)
    {
        $this->ensureAdministrator();
        $this->ensureEligible($abstractPost);

        $data = $this->letterData($abstractPost->load('user'));

        return response($letterPdf->render($data, $abstractPost->id))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$this->attachmentFilename($abstractPost).'"');
    }

    public function send(AbstractPost $abstractPost, AbstractAcceptanceLetterPdf $letterPdf)
    {
        $this->ensureAdministrator();
        $mailSent = false;
        $attachmentPath = null;

        try {
            DB::transaction(function () use ($abstractPost, $letterPdf, &$mailSent, &$attachmentPath) {
                $locked = AbstractPost::with('user')->whereKey($abstractPost->id)->lockForUpdate()->firstOrFail();
                if (!$this->isEligible($locked)) {
                    throw ValidationException::withMessages(['abstract' => 'This abstract is not eligible for an acceptance email.']);
                }

                $previous = AbstractPostEmail::where('abstract_post_id', $locked->id)
                    ->where('kind', 'acceptance')->where('status', 'sent')->first();
                if ($previous) {
                    throw ValidationException::withMessages(['abstract' => 'An acceptance email has already been sent for this abstract.']);
                }

                $data = $this->letterData($locked);
                $subject = $this->subjectLine($locked, $data['presenterName']);
                $bodyHtml = view('emails.abstract-acceptance', $data)->render();
                $attachmentFilename = $this->attachmentFilename($locked);
                $attachmentPath = 'abstract-post-emails/acceptance/'.$locked->id.'/'.Str::uuid().'.pdf';
                if (!Storage::disk('local')->put($attachmentPath, $letterPdf->render($data, $locked->id))) {
                    throw new \RuntimeException('Could not save the acceptance letter PDF.');
                }

                Mail::to($locked->user->email)->send(new AbstractAcceptance(
                    $subject,
                    $bodyHtml,
                    Storage::disk('local')->path($attachmentPath),
                    $attachmentFilename
                ));
                $mailSent = true;

                AbstractPostEmail::create([
                    'abstract_post_id' => $locked->id,
                    'sent_by_user_id' => auth()->id(),
                    'kind' => 'acceptance',
                    'recipient_email' => $locked->user->email,
                    'from_email' => config('mail.from.address'),
                    'reply_to_email' => AbstractAcceptance::REPLY_TO,
                    'subject' => $subject,
                    'body_html' => $bodyHtml,
                    'attachment_disk' => 'local',
                    'attachment_path' => $attachmentPath,
                    'attachment_filename' => $attachmentFilename,
                    'attachment_mime' => 'application/pdf',
                    'status' => 'sent',
                    'sent_at' => now(),
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
                : 'The acceptance email could not be sent. No email history was recorded.');
        }

        return redirect()->route('abstract_posts.acceptance_emails.index')
            ->with('success', 'The acceptance email and attached PDF were sent and recorded.');
    }

    private function letterData(AbstractPost $abstractPost): array
    {
        $user = $abstractPost->user;
        $title = trim((string) $abstractPost->title);
        if (!$user || !filter_var($user->email, FILTER_VALIDATE_EMAIL) || $title === '') {
            throw ValidationException::withMessages([
                'abstract' => 'The recipient must have a valid email, and the abstract must have a title before sending.',
            ]);
        }

        $presenterName = $abstractPost->presenterName();

        return [
            'abstractTitle' => $title,
            'presenterName' => $presenterName,
            'attentionName' => $presenterName,
            'presentationType' => $abstractPost->presentation_type,
            'presentationLabel' => $abstractPost->presentation_type === 'Oral Presentation' ? 'ORAL' : 'POSTER',
            'letterDate' => now('America/Lima')->format('F j, Y'),
        ];
    }

    private function subjectLine(AbstractPost $abstractPost, string $presenterName): string
    {
        $subject = 'NOTIFICATION OF ABSTRACT ACCEPTANCE N° '.$abstractPost->id;

        return $presenterName !== '' ? $subject.' - '.$presenterName : $subject;
    }

    private function attachmentFilename(AbstractPost $abstractPost): string
    {
        $format = $abstractPost->presentation_type === 'Oral Presentation' ? 'Oral' : 'Poster';

        return 'Abstract-Acceptance-'.$abstractPost->id.'-'.$format.'.pdf';
    }

    private function ensureEligible(AbstractPost $abstractPost): void
    {
        abort_unless($this->isEligible($abstractPost), 404);
        abort_if($abstractPost->emails()->where('kind', 'acceptance')->where('status', 'sent')->exists(), 404);
    }

    private function isEligible(AbstractPost $abstractPost): bool
    {
        return $abstractPost->status === 'accepted'
            && in_array($abstractPost->presentation_type, self::PRESENTATION_TYPES, true);
    }

    private function ensureAdministrator(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('Administrador'), 403);
    }
}
