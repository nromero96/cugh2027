<?php

namespace Tests\Feature;

use App\Mail\AbstractReviewInstructions;
use App\Models\ReviewerCandidate;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ReviewerInstructionsNotificationTest extends TestCase
{
    private const FORM_MIDDLEWARE = [
        \App\Http\Middleware\VerifyCsrfToken::class,
        \App\Http\Middleware\CheckInscription::class,
        \App\Http\Middleware\EnsureStatusActive::class,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'review_instructions_testing', 'database.connections.review_instructions_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
        });
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });
        Schema::create('reviewer_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('review_instructions_sent_at')->nullable();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Administrador', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'Participante', 'guard_name' => 'web'],
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function user(int $id, int $roleId): User
    {
        DB::table('users')->insert([
            'id' => $id,
            'name' => 'User '.$id,
            'email' => 'user'.$id.'@example.org',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => $roleId,
            'model_type' => User::class,
            'model_id' => $id,
        ]);
        return User::findOrFail($id);
    }

    public function test_administrator_can_send_selected_instruction_email_only_once()
    {
        Mail::fake();
        $this->actingAs($this->user(1, 1));
        $candidate = ReviewerCandidate::create([
            'first_name' => 'Ana',
            'last_name' => 'Reviewer',
            'email' => 'ana@example.org',
        ]);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('reviewer_candidates.review_instructions.send'), ['reviewer_ids' => [$candidate->id]])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        Mail::assertSent(AbstractReviewInstructions::class, function ($mail) {
            return $mail->hasTo('ana@example.org')
                && $mail->build()->subject === 'CUGH LIMA 2027 Abstract Review Process - (Ana Reviewer)';
        });
        $this->assertNotNull($candidate->fresh()->review_instructions_sent_at);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('reviewer_candidates.review_instructions.send'), ['reviewer_ids' => [$candidate->id]])
            ->assertSessionHasNoErrors();
        Mail::assertSent(AbstractReviewInstructions::class, 1);
    }

    public function test_non_administrator_cannot_send_instruction_emails()
    {
        Mail::fake();
        $this->actingAs($this->user(1, 2));

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('reviewer_candidates.review_instructions.send'), ['reviewer_ids' => [1]])
            ->assertForbidden();
        Mail::assertNothingSent();
    }

    public function test_instruction_email_contains_the_scoring_guide_image()
    {
        config(['services.correonotificacion.copy' => 'notifications@example.org']);
        $mail = new AbstractReviewInstructions('Ana Reviewer');
        $html = $mail->render();

        $this->assertStringContainsString('Dear SPAC member:', $html);
        $this->assertStringContainsString('scale of 0 to 10', $html);
        $this->assertStringContainsString('October 5 at 11:59 PM', $html);
        $this->assertStringContainsString('cid:', $html);
        $this->assertSame('CUGH LIMA 2027 Abstract Review Process - (Ana Reviewer)', $mail->subject);
        $this->assertTrue($mail->hasReplyTo('notifications@example.org'));
        $this->assertTrue($mail->hasBcc('notifications@example.org'));
    }
}
