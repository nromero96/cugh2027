<?php

namespace Tests\Feature;

use App\Http\Controllers\AbstractPostController;
use App\Mail\AbstractRejection;
use App\Mail\AbstractAcceptance;
use App\Exports\AbstractPostExport;
use App\Models\AbstractPost;
use App\Models\User;
use App\Services\AbstractReviewerAssignmentImportService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AbstractReviewerAssignmentTest extends TestCase
{
    private const FORM_MIDDLEWARE = [
        \App\Http\Middleware\VerifyCsrfToken::class,
        \App\Http\Middleware\CheckInscription::class,
        \App\Http\Middleware\EnsureStatusActive::class,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'abstract_assignment_testing', 'database.connections.abstract_assignment_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('salutation')->nullable();
            $table->string('lastname')->nullable();
            $table->string('second_lastname')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('status')->nullable();
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
        Schema::create('abstract_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('main_author_country_id')->nullable();
            $table->string('presentation_type')->nullable();
            $table->string('title')->nullable();
            $table->text('main_author')->nullable();
            $table->text('co_authors')->nullable();
            $table->text('institutions')->nullable();
            $table->string('abstract_type')->nullable();
            $table->string('subtopic')->nullable();
            $table->text('body')->nullable();
            $table->text('keywords')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('abstract_post_reviewers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('abstract_post_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedTinyInteger('score_1')->nullable();
            $table->unsignedTinyInteger('score_2')->nullable();
            $table->unsignedTinyInteger('score_3')->nullable();
            $table->unsignedTinyInteger('score_4')->nullable();
            $table->unsignedTinyInteger('score_5')->nullable();
            $table->decimal('average_score', 4, 2)->nullable();
            $table->text('reviewer_note')->nullable();
            $table->timestamps();
            $table->unique(['abstract_post_id', 'reviewer_id']);
        });
        Schema::create('abstract_post_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('abstract_post_id');
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            $table->string('status_change')->nullable();
            $table->timestamps();
        });
        Schema::create('abstract_post_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('abstract_post_id');
            $table->unsignedBigInteger('sent_by_user_id')->nullable();
            $table->string('kind');
            $table->string('recipient_email');
            $table->string('from_email')->nullable();
            $table->string('reply_to_email')->nullable();
            $table->string('subject');
            $table->text('body_html');
            $table->string('attachment_disk')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_filename')->nullable();
            $table->string('attachment_mime')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
        Schema::create('reviewer_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Administrador', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'Calificador', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'Participante', 'guard_name' => 'web'],
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function user(int $id, int $roleId): User
    {
        DB::table('users')->insert([
            'id' => $id,
            'name' => 'User '.$id,
            'salutation' => 'Ms.',
            'lastname' => 'Test',
            'email' => "user{$id}@example.com",
            'password' => bcrypt('password'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => $roleId,
            'model_type' => User::class,
            'model_id' => $id,
        ]);
        DB::table('reviewer_candidates')->insert([
            'email' => "user{$id}@example.com",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($id);
    }

    private function abstract(int $id, int $ownerId): AbstractPost
    {
        DB::table('abstract_posts')->insert([
            'id' => $id,
            'user_id' => $ownerId,
            'presentation_type' => 'Oral',
            'title' => 'Abstract '.$id,
            'main_author' => json_encode(['name' => 'Main', 'lastname' => 'Author']),
            'abstract_type' => 'Research',
            'status' => 'submitted',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return AbstractPost::findOrFail($id);
    }

    public function test_administrator_can_assign_and_replace_up_to_three_reviewers()
    {
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $reviewers = [
            $this->user(3, 2),
            $this->user(4, 2),
            $this->user(5, 2),
        ];
        $abstract = $this->abstract(1, $owner->id);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->put(route('abstract_posts.assignments.update', $abstract), [
            'reviewer_ids' => collect($reviewers)->pluck('id')->all(),
        ])->assertRedirect()->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertSame([3, 4, 5], $abstract->reviewers()->orderBy('users.id')->pluck('users.id')->all());

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->put(route('abstract_posts.assignments.update', $abstract), [
            'reviewer_ids' => [4],
        ])->assertSessionHasNoErrors();

        $this->assertSame([4], $abstract->reviewers()->pluck('users.id')->all());
    }

    public function test_server_rejects_more_than_three_reviewers()
    {
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        foreach (range(3, 6) as $id) {
            $this->user($id, 2);
        }
        $abstract = $this->abstract(1, $owner->id);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->put(route('abstract_posts.assignments.update', $abstract), [
            'reviewer_ids' => [3, 4, 5, 6],
        ])->assertSessionHasErrors('reviewer_ids');

        $this->assertDatabaseCount('abstract_post_reviewers', 0);
    }

    public function test_administrator_can_replace_only_a_pending_reviewer_slot()
    {
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $reviewed = $this->user(3, 2);
        $pending = $this->user(4, 2);
        $replacement = $this->user(5, 2);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->reviewers()->attach($reviewed->id, ['average_score' => 8, 'score_1' => 8]);
        $abstract->reviewers()->attach($pending->id);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.assignments.slot.update', [$abstract, 2]), [
                'current_reviewer_id' => $pending->id,
                'reviewer_id' => $replacement->id,
            ])->assertSessionHasNoErrors();

        $this->assertSame([$reviewed->id, $replacement->id], DB::table('abstract_post_reviewers')
            ->where('abstract_post_id', $abstract->id)->orderBy('id')->pluck('reviewer_id')->all());
        $this->assertDatabaseHas('abstract_post_reviewers', [
            'abstract_post_id' => $abstract->id,
            'reviewer_id' => $reviewed->id,
            'average_score' => 8,
        ]);
    }

    public function test_evaluated_slot_cannot_be_replaced_but_next_empty_slot_can_be_assigned()
    {
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $reviewed = $this->user(3, 2);
        $newReviewer = $this->user(4, 2);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->reviewers()->attach($reviewed->id, ['score_1' => 8]);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.assignments.slot.update', [$abstract, 1]), [
                'current_reviewer_id' => $reviewed->id,
                'reviewer_id' => $newReviewer->id,
            ])->assertSessionHasErrors('reviewer_id');
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.assignments.slot.update', [$abstract, 2]), [
                'reviewer_id' => $newReviewer->id,
            ])->assertSessionHasNoErrors();
        $this->assertSame([$reviewed->id, $newReviewer->id], DB::table('abstract_post_reviewers')
            ->where('abstract_post_id', $abstract->id)->orderBy('id')->pluck('reviewer_id')->all());
    }

    public function test_slot_change_rejects_duplicates_and_stale_assignments()
    {
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $first = $this->user(3, 2);
        $second = $this->user(4, 2);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->reviewers()->attach([$first->id, $second->id]);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.assignments.slot.update', [$abstract, 1]), [
                'current_reviewer_id' => $first->id,
                'reviewer_id' => $second->id,
            ])->assertSessionHasErrors('reviewer_id');
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.assignments.slot.update', [$abstract, 1]), [
                'current_reviewer_id' => $second->id,
                'reviewer_id' => $first->id,
            ])->assertSessionHasErrors('reviewer_id');
        $this->assertDatabaseCount('abstract_post_reviewers', 2);
    }

    public function test_slot_change_rejects_ineligible_reviewer_and_qualified_abstract()
    {
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $reviewer = $this->user(3, 2);
        $abstract = $this->abstract(1, $owner->id);
        $this->actingAs($admin);
        DB::table('reviewer_candidates')->where('email', $reviewer->email)->delete();

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.assignments.slot.update', [$abstract, 1]), [
                'reviewer_id' => $reviewer->id,
            ])->assertSessionHasErrors('reviewer_id');

        DB::table('reviewer_candidates')->insert(['email' => $reviewer->email]);
        $abstract->update(['status' => 'qualified']);
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.assignments.slot.update', [$abstract, 1]), [
                'reviewer_id' => $reviewer->id,
            ])->assertSessionHasErrors('reviewer_id');
        $this->assertDatabaseCount('abstract_post_reviewers', 0);
    }

    public function test_participant_can_be_assigned_without_the_reviewer_role()
    {
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $abstract = $this->abstract(1, $owner->id);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->put(route('abstract_posts.assignments.update', $abstract), [
            'reviewer_ids' => [$owner->id],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('abstract_post_reviewers', [
            'abstract_post_id' => $abstract->id,
            'reviewer_id' => $owner->id,
        ]);
    }

    public function test_non_administrator_cannot_manage_assignments()
    {
        $owner = $this->user(1, 3);
        $reviewer = $this->user(2, 2);
        $abstract = $this->abstract(1, $owner->id);
        $this->actingAs($reviewer);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->get(route('abstract_posts.assignments'))->assertForbidden();
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->put(route('abstract_posts.assignments.update', $abstract), [
            'reviewer_ids' => [$reviewer->id],
        ])->assertForbidden();
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->put(route('abstract_posts.assignments.slot.update', [$abstract, 1]), [
            'reviewer_id' => $reviewer->id,
        ])->assertForbidden();
    }

    public function test_assignment_page_lists_only_users_matching_reviewer_candidate_emails()
    {
        $admin = $this->user(1, 1);
        $matchingUser = $this->user(2, 3);
        $nonMatchingUser = $this->user(3, 3);
        DB::table('reviewer_candidates')->where('email', $nonMatchingUser->email)->delete();
        $this->actingAs($admin);

        $response = app(AbstractPostController::class)->reviewerAssignments(Request::create('/abstract-post-reviewer-assignments'));
        $reviewerIds = $response->getData()['reviewers']->pluck('id')->all();

        $this->assertContains($matchingUser->id, $reviewerIds);
        $this->assertNotContains($nonMatchingUser->id, $reviewerIds);
    }

    public function test_assignment_page_can_filter_a_reviewers_editable_abstracts()
    {
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $unavailableReviewer = $this->user(3, 2);
        $otherReviewer = $this->user(4, 2);
        $editable = $this->abstract(1, $owner->id);
        $locked = $this->abstract(2, $owner->id);
        $unrelated = $this->abstract(3, $owner->id);
        $editable->reviewers()->attach($unavailableReviewer->id);
        $editable->reviewers()->attach($otherReviewer->id, ['average_score' => 8]);
        $locked->reviewers()->attach($unavailableReviewer->id, ['average_score' => 8]);
        $unrelated->reviewers()->attach($otherReviewer->id);
        $this->actingAs($admin);

        $request = Request::create('/abstract-post-reviewer-assignments', 'GET', [
            'reviewer_id' => $unavailableReviewer->id,
            'assignment_state' => 'editable',
        ]);
        $response = app(AbstractPostController::class)->reviewerAssignments($request);
        $this->assertSame([$editable->id], $response->getData()['abstracts']->pluck('id')->all());
        $this->assertSame($unavailableReviewer->id, $response->getData()['assignedReviewerId']);

    }

    public function test_user_without_matching_reviewer_candidate_email_cannot_be_assigned()
    {
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $nonMatchingUser = $this->user(3, 3);
        DB::table('reviewer_candidates')->where('email', $nonMatchingUser->email)->delete();
        $abstract = $this->abstract(1, $owner->id);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->put(route('abstract_posts.assignments.update', $abstract), [
            'reviewer_ids' => [$nonMatchingUser->id],
        ])->assertSessionHasErrors('reviewer_ids');

        $this->assertDatabaseCount('abstract_post_reviewers', 0);
    }

    public function test_main_abstract_listing_does_not_include_assigned_abstracts()
    {
        $owner = $this->user(1, 3);
        $reviewer = $this->user(2, 2);
        $assigned = $this->abstract(1, $owner->id);
        $this->abstract(2, $owner->id);
        DB::table('abstract_post_reviewers')->insert([
            'abstract_post_id' => $assigned->id,
            'reviewer_id' => $reviewer->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs($reviewer);

        $response = app(AbstractPostController::class)->index(Request::create('/abstract-posts'));
        $ids = collect($response->getData()['abstract_posts']->items())->pluck('id')->all();

        $this->assertSame([], $ids);
    }

    public function test_assigned_reviewer_can_save_scores_average_and_note()
    {
        $owner = $this->user(1, 3);
        $reviewer = $this->user(2, 2);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->reviewers()->attach($reviewer->id);
        $this->actingAs($reviewer);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->put(route('abstract_posts.review', $abstract), [
            'score_1' => 8,
            'score_2' => 9,
            'score_3' => 7,
            'score_4' => 10,
            'score_5' => 7,
            'reviewer_note' => 'Clear and relevant abstract.',
        ])->assertRedirect()->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertDatabaseHas('abstract_post_reviewers', [
            'abstract_post_id' => $abstract->id,
            'reviewer_id' => $reviewer->id,
            'score_1' => 8,
            'score_5' => 7,
            'average_score' => 8.2,
            'reviewer_note' => 'Clear and relevant abstract.',
        ]);
        $this->assertSame('qualified', $abstract->fresh()->status);
    }

    public function test_submitted_evaluation_cannot_be_changed()
    {
        $owner = $this->user(1, 3);
        $reviewer = $this->user(2, 3);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->reviewers()->attach($reviewer->id);
        $this->actingAs($reviewer);

        $firstScores = ['score_1' => 6, 'score_2' => 6, 'score_3' => 6, 'score_4' => 6, 'score_5' => 6];
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.review', $abstract), $firstScores)
            ->assertSessionHasNoErrors();

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.review', $abstract), array_fill_keys(array_keys($firstScores), 10))
            ->assertSessionHasErrors('review');

        $this->assertDatabaseHas('abstract_post_reviewers', [
            'abstract_post_id' => $abstract->id,
            'reviewer_id' => $reviewer->id,
            'average_score' => 6,
        ]);
    }

    public function test_abstract_becomes_qualified_after_all_assigned_reviewers_submit()
    {
        $owner = $this->user(1, 3);
        $firstReviewer = $this->user(2, 3);
        $secondReviewer = $this->user(3, 3);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->reviewers()->attach([$firstReviewer->id, $secondReviewer->id]);
        $scores = ['score_1' => 8, 'score_2' => 8, 'score_3' => 8, 'score_4' => 8, 'score_5' => 8];

        $this->actingAs($firstReviewer);
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.review', $abstract), $scores)
            ->assertSessionHasNoErrors();
        $this->assertSame('submitted', $abstract->fresh()->status);

        $this->actingAs($secondReviewer);
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.review', $abstract), $scores)
            ->assertSessionHasNoErrors();
        $this->assertSame('qualified', $abstract->fresh()->status);
    }

    public function test_submitted_review_prevents_removing_its_reviewer()
    {
        $administrator = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $reviewer = $this->user(3, 3);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->reviewers()->attach($reviewer->id, [
            'score_1' => 8,
            'score_2' => 8,
            'score_3' => 8,
            'score_4' => 8,
            'score_5' => 8,
            'average_score' => 8,
        ]);
        $this->actingAs($administrator);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.assignments.update', $abstract), ['reviewer_ids' => []])
            ->assertSessionHasErrors('reviewer_ids');

        $this->assertDatabaseHas('abstract_post_reviewers', [
            'abstract_post_id' => $abstract->id,
            'reviewer_id' => $reviewer->id,
            'average_score' => 8,
        ]);
    }

    public function test_review_scores_must_be_whole_numbers_between_zero_and_ten()
    {
        $owner = $this->user(1, 3);
        $reviewer = $this->user(2, 2);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->reviewers()->attach($reviewer->id);
        $this->actingAs($reviewer);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->put(route('abstract_posts.review', $abstract), [
            'score_1' => -1,
            'score_2' => 11,
            'score_3' => 7.5,
            'score_4' => 8,
            'score_5' => 9,
        ])->assertSessionHasErrors(['score_1', 'score_2', 'score_3']);

        $this->assertNull(DB::table('abstract_post_reviewers')->value('average_score'));
    }

    public function test_unassigned_reviewer_cannot_submit_an_evaluation()
    {
        $owner = $this->user(1, 3);
        $reviewer = $this->user(2, 2);
        $abstract = $this->abstract(1, $owner->id);
        $this->actingAs($reviewer);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->put(route('abstract_posts.review', $abstract), [
            'score_1' => 8,
            'score_2' => 8,
            'score_3' => 8,
            'score_4' => 8,
            'score_5' => 8,
        ])->assertForbidden();
    }

    public function test_assigned_participant_can_submit_an_evaluation_without_reviewer_role()
    {
        $owner = $this->user(1, 3);
        $participantReviewer = $this->user(2, 3);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->reviewers()->attach($participantReviewer->id);
        $this->actingAs($participantReviewer);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)->put(route('abstract_posts.review', $abstract), [
            'score_1' => 0,
            'score_2' => 10,
            'score_3' => 10,
            'score_4' => 10,
            'score_5' => 10,
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertDatabaseHas('abstract_post_reviewers', [
            'abstract_post_id' => $abstract->id,
            'reviewer_id' => $participantReviewer->id,
            'score_1' => 0,
            'average_score' => 8,
        ]);
    }

    public function test_main_abstract_listing_contains_only_owned_abstracts()
    {
        $owner = $this->user(1, 3);
        $participantReviewer = $this->user(2, 3);
        $assigned = $this->abstract(1, $owner->id);
        $owned = $this->abstract(2, $participantReviewer->id);
        $assigned->reviewers()->attach($participantReviewer->id);
        $this->actingAs($participantReviewer);

        $response = app(AbstractPostController::class)->index(Request::create('/abstract-posts'));
        $ids = collect($response->getData()['abstract_posts']->items())->pluck('id')->all();

        $this->assertSame([$owned->id], $ids);
    }

    public function test_administrator_main_listing_and_report_include_all_users_abstracts()
    {
        $administrator = $this->user(1, 1);
        $otherUser = $this->user(2, 3);
        $owned = $this->abstract(1, $administrator->id);
        $this->abstract(2, $otherUser->id);
        $this->actingAs($administrator);

        $response = app(AbstractPostController::class)->index(Request::create('/abstract-posts'));
        $data = $response->getData();

        $this->assertSame([2, $owned->id], collect($data['abstract_posts']->items())->pluck('id')->all());
        $this->assertSame(2, (int) $data['abstractReport']->total);
    }

    public function test_administrator_listing_loads_assigned_reviewers_for_the_table()
    {
        $administrator = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $reviewer = $this->user(3, 2);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->reviewers()->attach($reviewer->id, ['average_score' => 8]);
        $this->actingAs($administrator);

        $response = app(AbstractPostController::class)->index(Request::create('/abstract-posts'));
        $listedAbstract = collect($response->getData()['abstract_posts']->items())->first();

        $this->assertTrue($listedAbstract->relationLoaded('reviewers'));
        $this->assertSame($reviewer->email, $listedAbstract->reviewers->first()->email);
        $this->assertSame(8.0, (float) $listedAbstract->reviewers->first()->pivot->average_score);
    }

    public function test_assigned_abstracts_page_lists_only_the_current_users_assignments()
    {
        $owner = $this->user(1, 3);
        $reviewer = $this->user(2, 3);
        $assigned = $this->abstract(1, $owner->id);
        $this->abstract(2, $reviewer->id);
        $assigned->reviewers()->attach($reviewer->id);
        $this->actingAs($reviewer);

        $response = app(AbstractPostController::class)->assignedAbstracts();
        $abstracts = collect($response->getData()['abstracts']->items());

        $this->assertSame('assigned_abstracts', $response->getData()['category_name']);
        $this->assertSame([$assigned->id], $abstracts->pluck('id')->all());
        $this->assertSame($reviewer->id, $abstracts->first()->pivot->reviewer_id);
    }

    public function test_user_without_assignments_cannot_open_assigned_abstracts_page()
    {
        $owner = $this->user(1, 3);
        $this->abstract(1, $owner->id);
        $this->actingAs($owner);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.assigned'))
            ->assertForbidden();
    }

    public function test_import_matches_uppercase_emails_and_returns_rejected_rows_without_partial_assignments()
    {
        $owner = $this->user(1, 3);
        $reviewer = $this->user(2, 3);
        $abstract = $this->abstract(1, $owner->id);
        $file = $this->assignmentCsv(
            "abstract_post_id,Revisor 1,Revisor 2,Revisor 3\n"
            ."1,USER2@EXAMPLE.COM,,\n"
            ."2,user2@example.com,missing@example.com,\n"
            ."1,user2@example.com,,\n"
        );

        try {
            $service = app(AbstractReviewerAssignmentImportService::class);
            $result = $service->import($file);

            $this->assertSame(1, $result['added']);
            $this->assertCount(2, $result['rejected_rows']);
            $this->assertSame([$reviewer->id], $abstract->fresh()->reviewers->pluck('id')->all());
            $this->assertStringContainsString('missing@example.com', $service->rejectedRowsCsv($result['rejected_rows']));
            $this->assertStringContainsString('Duplicate abstract_post_id', $service->rejectedRowsCsv($result['rejected_rows']));

            $secondImport = $service->import($file);
            $this->assertSame(0, $secondImport['added']);
            $this->assertSame(1, $secondImport['unchanged']);
            $this->assertCount(1, $abstract->fresh()->reviewers);
        } finally {
            @unlink($file->getRealPath());
        }
    }

    public function test_import_preserves_submitted_reviews_and_reports_over_capacity_rows()
    {
        $owner = $this->user(1, 3);
        $first = $this->user(2, 3);
        $second = $this->user(3, 3);
        $third = $this->user(4, 3);
        $fourth = $this->user(5, 3);
        $locked = $this->abstract(1, $owner->id);
        $full = $this->abstract(2, $owner->id);
        $locked->reviewers()->attach($first->id, ['average_score' => 7]);
        $full->reviewers()->attach([$first->id, $second->id, $third->id]);
        $file = $this->assignmentCsv(
            "abstract_post_id,Revisor 1,Revisor 2,Revisor 3\n"
            ."1,user5@example.com,,\n"
            ."2,user5@example.com,,\n"
        );

        try {
            $result = app(AbstractReviewerAssignmentImportService::class)->import($file);
            $this->assertSame(0, $result['added']);
            $this->assertCount(2, $result['rejected_rows']);
            $this->assertCount(1, $locked->fresh()->reviewers);
            $this->assertCount(3, $full->fresh()->reviewers);
        } finally {
            @unlink($file->getRealPath());
        }
    }

    public function test_import_page_saves_a_downloadable_error_report_for_administrators()
    {
        Storage::fake('local');
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $this->abstract(1, $owner->id);
        $file = $this->assignmentXlsx();
        $this->actingAs($admin);

        try {
            $response = $this->withoutMiddleware(self::FORM_MIDDLEWARE)
                ->post(route('abstract_posts.assignments.import'), ['assignment_file' => $file]);
            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('abstract_posts.assignments'));
            $response->assertSessionHas('abstract_assignment_import_report');
            $report = session('abstract_assignment_import_report');
            Storage::disk('local')->assertExists($report['path']);

            $this->withoutMiddleware(self::FORM_MIDDLEWARE)
                ->get(route('abstract_posts.assignments.import_errors', $report['token']))
                ->assertOk();
        } finally {
            @unlink($file->getRealPath());
        }
    }

    public function test_non_administrator_cannot_import_assignments()
    {
        $participant = $this->user(1, 3);
        $this->actingAs($participant);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.assignments.import'))
            ->assertForbidden();
    }

    private function assignmentCsv(string $contents): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'abstract_assign_');
        file_put_contents($path, $contents);
        return new UploadedFile($path, 'assignments.csv', 'text/csv', null, true);
    }

    public function test_administrator_can_accept_abstract_and_record_presentation_type_and_status_history()
    {
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $abstract = $this->abstract(1, $owner->id);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.accept', $abstract->id), ['presentation_type' => 'Poster'])
            ->assertRedirect(route('abstract_posts.show', $abstract->id));

        $this->assertDatabaseHas('abstract_posts', [
            'id' => $abstract->id,
            'presentation_type' => 'Poster',
            'status' => 'accepted',
        ]);
        $this->assertDatabaseHas('abstract_post_notes', [
            'abstract_post_id' => $abstract->id,
            'user_id' => $admin->id,
            'comment' => 'Changed presentation type from Oral to Poster.',
            'status_change' => 'Changed status from submitted to accepted',
        ]);
        $this->assertDatabaseCount('abstract_post_notes', 1);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.accept', $abstract->id), ['presentation_type' => 'Oral Presentation'])
            ->assertRedirect(route('abstract_posts.show', $abstract->id));
        $this->assertDatabaseHas('abstract_post_notes', [
            'abstract_post_id' => $abstract->id,
            'user_id' => $admin->id,
            'comment' => 'Changed presentation type from Poster to Oral Presentation.',
            'status_change' => 'Status remains accepted',
        ]);
        $this->assertDatabaseCount('abstract_post_notes', 2);
    }

    public function test_acceptance_rejects_invalid_presentation_type_and_non_administrator()
    {
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $abstract = $this->abstract(1, $owner->id);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.accept', $abstract->id), ['presentation_type' => 'Invalid'])
            ->assertSessionHasErrors('presentation_type');

        $this->actingAs($owner);
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.accept', $abstract->id), ['presentation_type' => 'Poster'])
            ->assertForbidden();

        $this->assertDatabaseHas('abstract_posts', [
            'id' => $abstract->id,
            'presentation_type' => 'Oral',
            'status' => 'submitted',
        ]);
        $this->assertDatabaseCount('abstract_post_notes', 0);
    }

    public function test_scientific_oral_acceptance_is_sent_once_and_keeps_accepted_status()
    {
        Storage::fake('local');
        Mail::fake();
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->update([
            'status' => 'accepted',
            'abstract_type' => 'Scientific Abstract',
            'presentation_type' => 'Oral Presentation',
            'title' => 'Scientific Research & Learning',
        ]);
        $owner->update(['second_lastname' => 'Surname']);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.acceptance_emails.preview', $abstract->id))
            ->assertOk()->assertSee('Good morning,');
        $pdfPreview = $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.acceptance_emails.letter', $abstract->id))
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $pdfPreview->baseResponse->getContent());

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.acceptance_emails.send', $abstract->id))
            ->assertRedirect(route('abstract_posts.acceptance_emails.index'));

        Mail::assertSent(AbstractAcceptance::class, function ($mail) use ($owner) {
            $mail->build();
            return $mail->hasTo($owner->email)
                && $mail->hasReplyTo(AbstractAcceptance::REPLY_TO)
                && $mail->subject === 'NOTIFICATION OF ABSTRACT ACCEPTANCE N° 1 - User 2 Test Surname'
                && count($mail->attachments) === 1
                && $mail->attachments[0]['options']['as'] === 'Abstract-Acceptance-1-Oral.pdf';
        });
        $this->assertDatabaseHas('abstract_posts', ['id' => $abstract->id, 'status' => 'accepted']);
        $this->assertDatabaseHas('abstract_post_emails', [
            'abstract_post_id' => $abstract->id,
            'recipient_email' => $owner->email,
            'subject' => 'NOTIFICATION OF ABSTRACT ACCEPTANCE N° 1 - User 2 Test Surname',
            'kind' => 'acceptance',
            'status' => 'sent',
            'attachment_filename' => 'Abstract-Acceptance-1-Oral.pdf',
        ]);
        $attachmentPath = DB::table('abstract_post_emails')->value('attachment_path');
        $emailId = DB::table('abstract_post_emails')->value('id');
        Storage::disk('local')->assertExists($attachmentPath);
        $this->assertStringStartsWith('%PDF', Storage::disk('local')->get($attachmentPath));
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.emails.show', $emailId))
            ->assertOk()->assertSee('Good morning,');
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.emails.attachment', $emailId))
            ->assertOk();

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.acceptance_emails.send', $abstract->id))
            ->assertSessionHasErrors('abstract');
        Mail::assertSent(AbstractAcceptance::class, 1);
        $this->assertDatabaseCount('abstract_post_emails', 1);
        $this->assertDatabaseHas('abstract_posts', ['id' => $abstract->id, 'status' => 'accepted']);

        $this->actingAs($owner);
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.acceptance_emails.index'))->assertForbidden();
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.acceptance_emails.letter', $abstract->id))->assertForbidden();
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.acceptance_emails.send', $abstract->id))->assertForbidden();
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.emails.show', $emailId))->assertForbidden();
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.emails.attachment', $emailId))->assertForbidden();
    }

    public function test_acceptance_list_includes_all_accepted_types_and_excludes_sent_until_searched()
    {
        Storage::fake('local');
        Mail::fake();
        $admin = $this->user(1, 1);
        $sentOwner = $this->user(2, 3);
        $pendingOwner = $this->user(3, 3);
        $sent = $this->abstract(1, $sentOwner->id);
        $sent->update(['status' => 'accepted', 'abstract_type' => 'Global Health Education Abstract', 'presentation_type' => 'Oral Presentation']);
        $pending = $this->abstract(2, $pendingOwner->id);
        $pending->update(['status' => 'accepted', 'abstract_type' => 'Global Health Education Abstract', 'presentation_type' => 'Poster']);
        $otherType = $this->abstract(3, $pendingOwner->id);
        $otherType->update(['status' => 'accepted', 'abstract_type' => 'Scientific Abstract', 'presentation_type' => 'Poster']);
        $panelType = $this->abstract(4, $pendingOwner->id);
        $panelType->update(['status' => 'accepted', 'abstract_type' => 'Program & Project Abstract', 'presentation_type' => 'Oral Presentation']);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.acceptance_emails.send', $sent->id))
            ->assertRedirect(route('abstract_posts.acceptance_emails.index'));
        $sentOwner->update(['email' => 'changed@example.com']);

        $controller = app(\App\Http\Controllers\AbstractAcceptanceController::class);
        $default = $controller->index(\Illuminate\Http\Request::create('/abstract-acceptance-emails'));
        $this->assertSame([2, 3, 4], $default->getData()['abstracts']->pluck('id')->all());

        $byId = $controller->index(\Illuminate\Http\Request::create('/abstract-acceptance-emails', 'GET', ['search' => '#1']));
        $this->assertSame([1], $byId->getData()['abstracts']->pluck('id')->all());
        $this->assertSame('sent', $byId->getData()['abstracts']->first()->emails->first()->status);

        $byOriginalEmail = $controller->index(\Illuminate\Http\Request::create('/abstract-acceptance-emails', 'GET', ['search' => 'USER2@EXAMPLE.COM']));
        $this->assertSame([1], $byOriginalEmail->getData()['abstracts']->pluck('id')->all());
    }

    public function test_program_and_project_poster_acceptance_uses_poster_attachment_and_optional_author()
    {
        Storage::fake('local');
        Mail::fake();
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $abstract = $this->abstract(2, $owner->id);
        $abstract->update([
            'status' => 'accepted',
            'abstract_type' => 'Program & Project Abstract',
            'presentation_type' => 'Poster',
            'main_author' => ['name' => '', 'lastname' => ''],
        ]);
        $owner->update(['name' => null, 'lastname' => null, 'second_lastname' => null]);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.acceptance_emails.letter', $abstract->id))
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.acceptance_emails.send', $abstract->id))
            ->assertRedirect(route('abstract_posts.acceptance_emails.index'));

        Mail::assertSent(AbstractAcceptance::class, function ($mail) {
            $mail->build();
            return $mail->subject === 'NOTIFICATION OF ABSTRACT ACCEPTANCE N° 2'
                && $mail->attachments[0]['options']['as'] === 'Abstract-Acceptance-2-Poster.pdf';
        });
        $this->assertDatabaseHas('abstract_posts', ['id' => $abstract->id, 'status' => 'accepted']);
        $this->assertDatabaseHas('abstract_post_emails', ['abstract_post_id' => $abstract->id, 'kind' => 'acceptance', 'status' => 'sent']);
    }

    public function test_acceptance_rejects_unsupported_presentation_and_status_and_failed_delivery()
    {
        Storage::fake('local');
        Mail::fake();
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $unsupportedPresentation = $this->abstract(1, $owner->id);
        $unsupportedPresentation->update(['status' => 'accepted', 'abstract_type' => 'Scientific Abstract', 'presentation_type' => 'Oral']);
        $wrongStatus = $this->abstract(2, $owner->id);
        $wrongStatus->update(['abstract_type' => 'Global Health Education Abstract', 'presentation_type' => 'Poster']);
        $eligible = $this->abstract(3, $owner->id);
        $eligible->update(['status' => 'accepted', 'abstract_type' => 'Global Health Education Abstract', 'presentation_type' => 'Poster']);
        $this->actingAs($admin);

        foreach ([$unsupportedPresentation, $wrongStatus] as $abstract) {
            $this->withoutMiddleware(self::FORM_MIDDLEWARE)
                ->post(route('abstract_posts.acceptance_emails.send', $abstract->id))
                ->assertSessionHasErrors('abstract');
            $this->withoutMiddleware(self::FORM_MIDDLEWARE)
                ->get(route('abstract_posts.acceptance_emails.letter', $abstract->id))
                ->assertNotFound();
        }
        Mail::assertNothingSent();

        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Simulated SMTP failure'));
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.acceptance_emails.send', $eligible->id))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('abstract_posts', ['id' => $eligible->id, 'status' => 'accepted']);
        $this->assertDatabaseCount('abstract_post_emails', 0);
        $this->assertSame([], Storage::disk('local')->allFiles('abstract-post-emails/acceptance'));
    }

    public function test_rejection_email_is_personalized_logged_once_and_rejects_only_its_abstract()
    {
        Storage::fake('local');
        Mail::fake();
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $first = $this->abstract(1, $owner->id);
        $second = $this->abstract(2, $owner->id);
        $first->update(['title' => 'First <abstract>']);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.rejection_emails.preview', $first->id))
            ->assertOk()
            ->assertSee('Good morning,')
            ->assertSee('Rosa Sheen');

        $pdfPreview = $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.rejection_emails.letter', $first->id))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $pdfPreview->baseResponse->getContent());

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.rejection_emails.send', $first->id))
            ->assertRedirect(route('abstract_posts.rejection_emails.index'));

        Mail::assertSent(AbstractRejection::class, function ($mail) use ($owner) {
            $mail->build();
            return $mail->hasTo($owner->email)
                && $mail->hasReplyTo(AbstractRejection::REPLY_TO)
                && $mail->subject === 'NOTIFICATION OF ABSTRACT DECISION N° 1 - Main Author'
                && count($mail->attachments) === 1
                && $mail->attachments[0]['options']['as'] === 'Abstract-Rejection-000001.pdf'
                && is_file($mail->attachments[0]['file']);
        });
        Mail::assertSent(AbstractRejection::class, 1);
        $this->assertDatabaseHas('abstract_posts', ['id' => $first->id, 'status' => 'not_accepted']);
        $this->assertDatabaseHas('abstract_posts', ['id' => $second->id, 'status' => 'submitted']);
        $this->assertDatabaseHas('abstract_post_emails', [
            'abstract_post_id' => $first->id,
            'recipient_email' => $owner->email,
            'reply_to_email' => AbstractRejection::REPLY_TO,
            'subject' => 'NOTIFICATION OF ABSTRACT DECISION N° 1 - Main Author',
            'attachment_disk' => 'local',
            'attachment_filename' => 'Abstract-Rejection-000001.pdf',
            'attachment_mime' => 'application/pdf',
            'status' => 'sent',
            'kind' => 'rejection',
            'sent_by_user_id' => $admin->id,
        ]);
        $this->assertStringContainsString('Good morning,', DB::table('abstract_post_emails')->value('body_html'));
        $attachmentPath = DB::table('abstract_post_emails')->value('attachment_path');
        Storage::disk('local')->assertExists($attachmentPath);
        $this->assertStringStartsWith('%PDF', Storage::disk('local')->get($attachmentPath));
        $this->assertNotNull(DB::table('abstract_post_emails')->value('sent_at'));
        $emailId = DB::table('abstract_post_emails')->value('id');
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.emails.show', $emailId))
            ->assertOk()
            ->assertSee('Rosa Sheen');
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.emails.attachment', $emailId))
            ->assertOk();
        $this->assertDatabaseHas('abstract_post_notes', [
            'abstract_post_id' => $first->id,
            'status_change' => 'Changed status from submitted to not_accepted',
        ]);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.rejection_emails.send', $first->id))
            ->assertSessionHasErrors('abstract');
        Mail::assertSent(AbstractRejection::class, 1);
        $this->assertDatabaseCount('abstract_post_emails', 1);

        $this->actingAs($owner);
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.emails.show', $emailId))
            ->assertForbidden();
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.emails.attachment', $emailId))
            ->assertForbidden();
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.rejection_emails.letter', $second->id))
            ->assertForbidden();
    }

    public function test_rejection_email_allows_missing_recipient_name_but_requires_email_and_administrator()
    {
        Storage::fake('local');
        Mail::fake();
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $abstract = $this->abstract(1, $owner->id);

        $this->actingAs($owner);
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.rejection_emails.send', $abstract->id))
            ->assertForbidden();

        $owner->update(['salutation' => null, 'name' => null, 'lastname' => null, 'second_lastname' => null]);
        $this->actingAs($admin);
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.rejection_emails.letter', $abstract->id))
            ->assertOk();
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.rejection_emails.send', $abstract->id))
            ->assertRedirect(route('abstract_posts.rejection_emails.index'));

        Mail::assertSent(AbstractRejection::class, 1);
        $this->assertDatabaseHas('abstract_posts', ['id' => $abstract->id, 'status' => 'not_accepted']);
        $this->assertDatabaseCount('abstract_post_emails', 1);

        $owner->update(['email' => 'invalid-email']);
        $second = $this->abstract(2, $owner->id);
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.rejection_emails.send', $second->id))
            ->assertSessionHasErrors('abstract');

        Mail::assertSent(AbstractRejection::class, 1);
        $this->assertDatabaseHas('abstract_posts', ['id' => $second->id, 'status' => 'submitted']);
        $this->assertDatabaseCount('abstract_post_emails', 1);
    }

    public function test_rejection_email_can_be_sent_without_presenter_name()
    {
        Storage::fake('local');
        Mail::fake();
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $abstract = $this->abstract(1, $owner->id);
        $abstract->update(['main_author' => ['name' => '', 'lastname' => '']]);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.rejection_emails.send', $abstract->id))
            ->assertRedirect(route('abstract_posts.rejection_emails.index'));

        Mail::assertSent(AbstractRejection::class, function ($mail) use ($owner) {
            $mail->build();
            return $mail->hasTo($owner->email)
                && $mail->subject === 'NOTIFICATION OF ABSTRACT DECISION N° 1';
        });
        $this->assertDatabaseHas('abstract_posts', ['id' => $abstract->id, 'status' => 'not_accepted']);
        $this->assertDatabaseHas('abstract_post_emails', [
            'abstract_post_id' => $abstract->id,
            'subject' => 'NOTIFICATION OF ABSTRACT DECISION N° 1',
            'status' => 'sent',
        ]);
    }

    public function test_draft_and_qualified_abstracts_can_receive_rejection_but_accepted_cannot()
    {
        Storage::fake('local');
        Mail::fake();
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $reviewer = $this->user(3, 2);
        $draft = $this->abstract(1, $owner->id);
        $qualified = $this->abstract(2, $owner->id);
        $accepted = $this->abstract(3, $owner->id);
        $this->abstract(4, $owner->id);
        $draft->update(['status' => 'draft']);
        $draft->reviewers()->attach($reviewer->id);
        $qualified->update(['status' => 'qualified']);
        $accepted->update(['status' => 'accepted']);
        $owner->update(['salutation' => null, 'name' => null, 'lastname' => null, 'second_lastname' => null]);
        $this->actingAs($admin);

        $listing = app(\App\Http\Controllers\AbstractRejectionController::class)->index(\Illuminate\Http\Request::create('/abstract-rejection-emails'));
        $this->assertSame([1, 2, 4], $listing->getData()['abstracts']->pluck('id')->all());

        foreach ([$draft, $qualified] as $abstract) {
            $this->withoutMiddleware(self::FORM_MIDDLEWARE)
                ->post(route('abstract_posts.rejection_emails.send', $abstract->id))
                ->assertRedirect(route('abstract_posts.rejection_emails.index'));
            $this->assertDatabaseHas('abstract_posts', ['id' => $abstract->id, 'status' => 'not_accepted']);
        }

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.rejection_emails.send', $accepted->id))
            ->assertSessionHasErrors('abstract');
        Mail::assertSent(AbstractRejection::class, 2);
        $this->assertDatabaseHas('abstract_posts', ['id' => $accepted->id, 'status' => 'accepted']);
        $this->assertDatabaseCount('abstract_post_emails', 2);

        $this->actingAs($reviewer);
        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('abstract_posts.review', $draft->id), [
                'score_1' => 8, 'score_2' => 8, 'score_3' => 8, 'score_4' => 8, 'score_5' => 8,
            ])
            ->assertSessionHasErrors('review');
    }

    public function test_rejection_list_search_finds_pending_and_sent_notices_by_id_and_email()
    {
        Storage::fake('local');
        Mail::fake();
        $admin = $this->user(1, 1);
        $sentOwner = $this->user(2, 3);
        $pendingOwner = $this->user(3, 3);
        $sentAbstract = $this->abstract(1, $sentOwner->id);
        $this->abstract(2, $pendingOwner->id);
        $this->actingAs($admin);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.rejection_emails.send', $sentAbstract->id))
            ->assertRedirect(route('abstract_posts.rejection_emails.index'));
        $sentOwner->update(['email' => 'new-address@example.com']);

        $controller = app(\App\Http\Controllers\AbstractRejectionController::class);
        $listing = $controller->index(\Illuminate\Http\Request::create('/abstract-rejection-emails'));
        $this->assertSame([2], $listing->getData()['abstracts']->pluck('id')->all());

        $byId = $controller->index(\Illuminate\Http\Request::create('/abstract-rejection-emails', 'GET', ['search' => '#1']));
        $this->assertSame([1], $byId->getData()['abstracts']->pluck('id')->all());
        $this->assertSame('sent', $byId->getData()['abstracts']->first()->emails->first()->status);

        $byOldEmail = $controller->index(\Illuminate\Http\Request::create('/abstract-rejection-emails', 'GET', ['search' => 'USER2@EXAMPLE.COM']));
        $this->assertSame([1], $byOldEmail->getData()['abstracts']->pluck('id')->all());

        $byPendingEmail = $controller->index(\Illuminate\Http\Request::create('/abstract-rejection-emails', 'GET', ['search' => 'user3@example.com']));
        $this->assertSame([2], $byPendingEmail->getData()['abstracts']->pluck('id')->all());
    }

    public function test_failed_rejection_email_leaves_status_and_history_unchanged()
    {
        Storage::fake('local');
        $admin = $this->user(1, 1);
        $owner = $this->user(2, 3);
        $abstract = $this->abstract(1, $owner->id);
        $this->actingAs($admin);
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Simulated SMTP failure'));

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->post(route('abstract_posts.rejection_emails.send', $abstract->id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('abstract_posts', ['id' => $abstract->id, 'status' => 'submitted']);
        $this->assertDatabaseCount('abstract_post_emails', 0);
        $this->assertDatabaseCount('abstract_post_notes', 0);
        $this->assertSame([], Storage::disk('local')->allFiles('abstract-post-emails/rejection'));
    }

    public function test_abstract_export_appends_review_scores_and_final_average()
    {
        $owner = $this->user(1, 3);
        foreach (range(2, 4) as $id) {
            $this->user($id, 2);
        }
        $abstract = $this->abstract(1, $owner->id);
        $abstract->reviewers()->attach(2, [
            'score_1' => 8, 'score_2' => 8, 'score_3' => 8, 'score_4' => 8, 'score_5' => 7,
            'average_score' => 7.8,
            'reviewer_note' => '=1+1',
        ]);
        $abstract->reviewers()->attach(3, [
            'score_1' => 8, 'score_2' => 8, 'score_3' => 8, 'score_4' => 8, 'score_5' => 8,
            'average_score' => 8.0,
            'reviewer_note' => 'Well structured abstract.',
        ]);
        $abstract->reviewers()->attach(4);

        $export = new AbstractPostExport();
        $exported = $export->collection()->first();
        $pendingRow = $export->map($exported);
        $this->assertCount(40, $export->headings());
        $this->assertCount(40, $pendingRow);
        $this->assertSame('Reviewer 1 Email', $export->headings()[15]);
        $this->assertSame('Reviewer 1 Note', $export->headings()[22]);
        $this->assertSame('Reviewer 2 Note', $export->headings()[30]);
        $this->assertSame('Reviewer 3 Note', $export->headings()[38]);
        $this->assertSame('Final Average', $export->headings()[39]);
        $this->assertSame('user2@example.com', $pendingRow[15]);
        $this->assertSame(8, $pendingRow[16]);
        $this->assertSame(7.8, $pendingRow[21]);
        $this->assertSame('=1+1', $pendingRow[22]);
        $this->assertSame('Well structured abstract.', $pendingRow[30]);
        $this->assertSame('user4@example.com', $pendingRow[31]);
        $this->assertNull($pendingRow[32]);
        $this->assertNull($pendingRow[38]);
        $this->assertNull($pendingRow[39]);

        $abstract->reviewers()->updateExistingPivot(4, [
            'score_1' => 8, 'score_2' => 8, 'score_3' => 8, 'score_4' => 8, 'score_5' => 7,
            'average_score' => 7.8,
            'reviewer_note' => 'Practical recommendations.',
        ]);
        $completedRow = $export->map($export->collection()->first());
        $this->assertSame('Practical recommendations.', $completedRow[38]);
        $this->assertSame(7.86, $completedRow[39]);

        $path = tempnam(sys_get_temp_dir(), 'abstract_export_').'.xlsx';
        try {
            file_put_contents($path, Excel::raw($export, ExcelFormat::XLSX));
            $book = IOFactory::load($path);
            $sheet = $book->getActiveSheet();
            $this->assertSame('Reviewer 1 Email', $sheet->getCell('P1')->getValue());
            $this->assertSame('Reviewer 1 Note', $sheet->getCell('W1')->getValue());
            $this->assertSame('Reviewer 2 Note', $sheet->getCell('AE1')->getValue());
            $this->assertSame('Reviewer 3 Note', $sheet->getCell('AM1')->getValue());
            $this->assertSame('=1+1', $sheet->getCell('W2')->getValue());
            $this->assertSame('s', $sheet->getCell('W2')->getDataType());
            $this->assertSame('Well structured abstract.', $sheet->getCell('AE2')->getValue());
            $this->assertSame('Practical recommendations.', $sheet->getCell('AM2')->getValue());
            $this->assertSame('Final Average', $sheet->getCell('AN1')->getValue());
            $this->assertSame(7.86, (float) $sheet->getCell('AN2')->getValue());
            $this->assertSame('0.00', $sheet->getStyle('AN2')->getNumberFormat()->getFormatCode());
            foreach (['P' => ['FF28539A', 'FFEDF3FC'], 'X' => ['FF087F8C', 'FFEAF7F7'], 'AF' => ['FFA05A00', 'FFFFF4E3']] as $column => $colors) {
                $this->assertSame($colors[0], $sheet->getStyle($column.'1')->getFill()->getStartColor()->getARGB());
                $this->assertSame($colors[1], $sheet->getStyle($column.'2')->getFill()->getStartColor()->getARGB());
                $this->assertSame('FFFFFFFF', $sheet->getStyle($column.'1')->getFont()->getColor()->getARGB());
            }
            $this->assertSame('FFCC1F2F', $sheet->getStyle('AN1')->getFill()->getStartColor()->getARGB());
            $this->assertSame('FFCC1F2F', $sheet->getStyle('AN2')->getFill()->getStartColor()->getARGB());
            $this->assertSame('FFFFFFFF', $sheet->getStyle('AN2')->getFont()->getColor()->getARGB());
            foreach (['A1', 'J2', 'W2', 'AE2', 'AM2', 'AN2'] as $cell) {
                $borders = $sheet->getStyle($cell)->getBorders();
                $this->assertSame('thin', $borders->getLeft()->getBorderStyle());
                $this->assertSame('thin', $borders->getRight()->getBorderStyle());
                $this->assertSame('thin', $borders->getTop()->getBorderStyle());
                $this->assertSame('thin', $borders->getBottom()->getBorderStyle());
                $this->assertSame('FFD0D6DE', $borders->getLeft()->getColor()->getARGB());
            }
            $book->disconnectWorksheets();
        } finally {
            @unlink($path);
        }
    }

    public function test_abstract_export_marks_download_ready_for_the_button()
    {
        $admin = $this->user(1, 1);
        $this->abstract(1, $admin->id);
        $this->actingAs($admin);
        $token = str_repeat('a', 32);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->get(route('abstract_posts.exportexcel', ['export_token' => $token]))
            ->assertOk()
            ->assertPlainCookie('abstract_export_ready', $token);
    }

    private function assignmentXlsx(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'abstract_assign_');
        $xlsxPath = $path.'.xlsx';
        rename($path, $xlsxPath);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray([
            ['abstract_post_id', 'Revisor 1', 'Revisor 2', 'Revisor 3'],
            [1, 'missing@example.com', null, null],
        ]);
        (new Xlsx($spreadsheet))->save($xlsxPath);
        $spreadsheet->disconnectWorksheets();
        return new UploadedFile($xlsxPath, 'assignments.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

}
