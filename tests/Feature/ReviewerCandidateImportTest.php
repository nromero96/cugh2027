<?php

namespace Tests\Feature;

use App\Http\Controllers\ReviewerCandidateController;
use App\Mail\ReviewerAccountCreated;
use App\Models\ReviewerCandidate;
use App\Models\User;
use App\Services\ReviewerCandidateImportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class ReviewerCandidateImportTest extends TestCase
{
    use DatabaseTransactions;

    private function actingAsAdministrator()
    {
        $administrator = Mockery::mock(User::class)->makePartial();
        $administrator->id = 999999;
        $administrator->shouldReceive('hasRole')->with('Administrador')->andReturn(true);
        $this->actingAs($administrator);
    }

    public function test_it_imports_and_updates_duplicate_reviewers_by_normalized_email()
    {
        $path = tempnam(sys_get_temp_dir(), 'reviewers_').'.csv';
        $stream = fopen($path, 'w');
        fputcsv($stream, array_values(ReviewerCandidateImportService::HEADERS));
        fputcsv($stream, [
            'Ana', 'Torres', 'Dr.', 'Professor', 'Example University', 'Peru',
            'ANA.TORRES@example.org', 'Yes', 'English; Spanish', 'Health systems',
        ]);
        fputcsv($stream, [
            'Ana', 'Torres Updated', 'Dr.', 'Professor', 'Example University', 'Peru',
            'ana.torres@example.org', 'Yes', 'English; Spanish', 'Health systems',
        ]);
        fclose($stream);

        try {
            $file = new UploadedFile($path, 'reviewers.csv', 'text/csv', null, true);
            $result = app(ReviewerCandidateImportService::class)->import($file);

            $this->assertSame(1, $result['created']);
            $this->assertSame(0, $result['updated']);
            $this->assertSame(1, $result['skipped']);
            $this->assertDatabaseHas('reviewer_candidates', [
                'email' => 'ana.torres@example.org',
                'last_name' => 'Torres Updated',
            ]);
        } finally {
            @unlink($path);
        }
    }

    public function test_header_normalization_ignores_non_breaking_and_repeated_spaces()
    {
        $importer = app(ReviewerCandidateImportService::class);

        $this->assertSame(
            'professional position academic title',
            $importer->normalizedHeader(" Professional\xC2\xA0 Position /  Academic Title ")
        );
    }

    public function test_rejected_rows_can_be_exported_with_the_reason_and_reimported()
    {
        $path = tempnam(sys_get_temp_dir(), 'reviewers_').'.csv';
        $stream = fopen($path, 'w');
        fputcsv($stream, array_values(ReviewerCandidateImportService::HEADERS));
        fputcsv($stream, [
            'Invalid', 'Email', 'Dr.', 'Professor', 'Example University', 'Peru',
            'not-an-email', 'Yes', 'English', 'Health systems',
        ]);
        fputcsv($stream, [
            'Ana', 'First Version', 'Dr.', 'Professor', 'Example University', 'Peru',
            'ana@example.org', 'Yes', 'English', 'Health systems',
        ]);
        fputcsv($stream, [
            'Ana', 'Final Version', 'Dr.', 'Professor', 'Example University', 'Peru',
            'ANA@example.org', 'Yes', 'English', 'Health systems',
        ]);
        fclose($stream);

        try {
            $importer = app(ReviewerCandidateImportService::class);
            $file = new UploadedFile($path, 'reviewers.csv', 'text/csv', null, true);
            $result = $importer->import($file);
            $csv = $importer->rejectedRowsCsv($result['rejected_rows']);

            $this->assertSame(1, $result['created']);
            $this->assertSame(2, $result['skipped']);
            $this->assertCount(2, $result['rejected_rows']);
            $this->assertStringContainsString('Import Error', $csv);
            $this->assertStringContainsString('not-an-email', $csv);
            $this->assertStringContainsString('duplicate email in the import file', $csv);
            $this->assertDatabaseHas('reviewer_candidates', [
                'email' => 'ana@example.org',
                'last_name' => 'Final Version',
            ]);
        } finally {
            @unlink($path);
        }
    }

    public function test_an_entirely_invalid_file_still_produces_a_rejected_rows_report()
    {
        $path = tempnam(sys_get_temp_dir(), 'reviewers_').'.csv';
        $stream = fopen($path, 'w');
        fputcsv($stream, array_values(ReviewerCandidateImportService::HEADERS));
        fputcsv($stream, [
            'Invalid', 'Reviewer', '', '', '', '', 'invalid-email', '', '', '',
        ]);
        fclose($stream);

        try {
            $file = new UploadedFile($path, 'invalid-reviewers.csv', 'text/csv', null, true);
            $result = app(ReviewerCandidateImportService::class)->import($file);

            $this->assertSame(0, $result['created']);
            $this->assertSame(1, $result['skipped']);
            $this->assertCount(1, $result['rejected_rows']);
            $this->assertSame('invalid-email', $result['rejected_rows'][0]['email']);
        } finally {
            @unlink($path);
        }
    }

    public function test_administrator_can_create_participant_account_and_email_credentials()
    {
        Mail::fake();
        config(['services.correonotificacion.copy' => 'notifications@example.org']);
        $this->actingAsAdministrator();
        $email = 'reviewer-'.uniqid().'@example.org';
        $candidate = ReviewerCandidate::create([
            'first_name' => 'Ana',
            'last_name' => 'Reviewer',
            'salutation' => 'Dr.',
            'professional_position_academic_title' => 'Professor',
            'institution' => 'Example University',
            'email' => $email,
        ]);

        $response = app(ReviewerCandidateController::class)->createUser($candidate);
        $user = User::where('email', $email)->firstOrFail();

        $this->assertTrue($response->isRedirect());
        $this->assertTrue($user->hasRole('Participante'));
        $this->assertDatabaseHas('inscriptions', [
            'user_id' => $user->id,
            'status' => 'Draft',
        ]);
        Mail::assertSent(ReviewerAccountCreated::class, function ($mail) use ($user, $email) {
            $html = view('emails.reviewer-account-created', [
                'user' => $mail->user,
                'plainPassword' => $mail->plainPassword,
            ])->render();

            return $mail->hasTo($email)
                && $mail->hasBcc('notifications@example.org')
                && $mail->build()->subject === 'CUGH 2027 - Your reviewer account (Ana Reviewer)'
                && $mail->user->is($user)
                && Hash::check($mail->plainPassword, $user->password)
                && strlen($mail->plainPassword) === 16
                && strpos($html, $email) !== false
                && strpos($html, $mail->plainPassword) !== false
                && strpos($html, 'We would like to thank you for accepting to serve as a reviewer') !== false
                && strpos($html, '#CC1F2F') !== false;
        });
    }

    public function test_existing_user_is_not_recreated_or_emailed()
    {
        Mail::fake();
        $this->actingAsAdministrator();
        $existingUser = User::whereNotNull('email')->firstOrFail();
        $candidate = ReviewerCandidate::firstOrCreate(
            ['email' => $existingUser->email],
            ['first_name' => $existingUser->name ?: 'Existing']
        );
        $userCount = User::count();

        $response = app(ReviewerCandidateController::class)->createUser($candidate);

        $this->assertTrue($response->isRedirect());
        $this->assertSame($userCount, User::count());
        Mail::assertNothingSent();
    }
}
