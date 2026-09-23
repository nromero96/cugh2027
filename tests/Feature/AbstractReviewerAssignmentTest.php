<?php

namespace Tests\Feature;

use App\Http\Controllers\AbstractPostController;
use App\Models\AbstractPost;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AbstractReviewerAssignmentTest extends TestCase
{
    private const FORM_MIDDLEWARE = [
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
            $table->string('presentation_type')->nullable();
            $table->string('title')->nullable();
            $table->text('main_author')->nullable();
            $table->string('abstract_type')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
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
            'score_5' => 6,
            'reviewer_note' => 'Clear and relevant abstract.',
        ])->assertRedirect()->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertDatabaseHas('abstract_post_reviewers', [
            'abstract_post_id' => $abstract->id,
            'reviewer_id' => $reviewer->id,
            'score_1' => 8,
            'score_5' => 6,
            'average_score' => 8,
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

}
