<?php

namespace Tests\Feature;

use App\Http\Controllers\PanelController;
use App\Models\Panel;
use App\Models\User;
use App\Services\PanelReviewerAssignmentImportService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PanelReviewerWorkflowTest extends TestCase
{
    private const FORM_MIDDLEWARE = [
        \App\Http\Middleware\VerifyCsrfToken::class,
        \App\Http\Middleware\CheckInscription::class,
        \App\Http\Middleware\EnsureStatusActive::class,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'panel_review_testing', 'database.connections.panel_review_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);

        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('lastname')->nullable();
            $table->string('second_lastname')->nullable(); $table->string('email')->unique(); $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('guard_name')->default('web');
        });
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id'); $table->string('model_type'); $table->unsignedBigInteger('model_id');
        });
        Schema::create('panels', function (Blueprint $table) {
            $table->id(); $table->string('title')->nullable(); $table->string('contact_email')->nullable();
            $table->string('status')->default('Submitted'); $table->timestamps();
        });
        Schema::create('panel_reviewers', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('panel_id'); $table->unsignedBigInteger('reviewer_id');
            for ($number = 1; $number <= 8; $number++) $table->unsignedTinyInteger('score_'.$number)->nullable();
            $table->decimal('average_score', 4, 2)->nullable(); $table->text('reviewer_note')->nullable();
            $table->timestamps(); $table->unique(['panel_id', 'reviewer_id']);
        });
        Schema::create('reviewer_candidates', function (Blueprint $table) {
            $table->id(); $table->string('email')->unique(); $table->timestamps();
        });
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Administrador', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'Participante', 'guard_name' => 'web'],
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function user(int $id, int $roleId): User
    {
        DB::table('users')->insert(['id' => $id, 'name' => 'User '.$id, 'email' => "user{$id}@example.org"]);
        DB::table('model_has_roles')->insert(['role_id' => $roleId, 'model_type' => User::class, 'model_id' => $id]);
        return User::findOrFail($id);
    }

    private function panel(): Panel
    {
        DB::table('panels')->insert(['id' => 1, 'title' => 'Test panel', 'status' => 'Submitted']);
        return Panel::findOrFail(1);
    }

    public function test_reviewers_can_score_once_and_panel_qualifies_after_all_reviews()
    {
        $admin = $this->user(1, 1);
        $first = $this->user(2, 2);
        $second = $this->user(3, 2);
        $panel = $this->panel();
        DB::table('reviewer_candidates')->insert([['email' => $first->email], ['email' => $second->email]]);

        $this->actingAs($admin);
        $this->withoutMiddleware(self::FORM_MIDDLEWARE);
        $this->put(route('panels.assignments.update', $panel), ['reviewer_ids' => [2, 3]])->assertSessionHasNoErrors();
        $this->assertSame(2, $panel->reviewers()->count());

        $scores = array_fill_keys(array_map(function ($number) { return 'score_'.$number; }, range(1, 8)), 7);
        $this->actingAs($first)->put(route('panels.review', $panel), $scores)->assertSessionHasNoErrors();
        $this->assertSame('Submitted', $panel->fresh()->status);
        $this->actingAs($first)->put(route('panels.review', $panel), $scores)->assertSessionHasErrors('review');
        $this->actingAs($second)->put(route('panels.review', $panel), $scores)->assertSessionHasNoErrors();
        $this->assertSame('Qualified', $panel->fresh()->status);
        $this->actingAs($admin)->put(route('panels.assignments.update', $panel), ['reviewer_ids' => [2]])->assertSessionHasErrors('reviewer_ids');
        $this->assertSame(2, $panel->reviewers()->count());
    }

    public function test_only_eligible_reviewers_can_be_assigned_and_scores_must_be_complete()
    {
        $admin = $this->user(1, 1);
        $reviewer = $this->user(2, 2);
        $panel = $this->panel();
        $this->withoutMiddleware(self::FORM_MIDDLEWARE);
        $this->actingAs($admin)->put(route('panels.assignments.update', $panel), ['reviewer_ids' => [2]])->assertSessionHasErrors('reviewer_ids');
        DB::table('reviewer_candidates')->insert(['email' => $reviewer->email]);
        $this->actingAs($admin)->put(route('panels.assignments.update', $panel), ['reviewer_ids' => [2]])->assertSessionHasNoErrors();
        $this->actingAs($reviewer)->put(route('panels.review', $panel), ['score_1' => 5])->assertSessionHasErrors('score_8');
        $invalidScores = array_fill_keys(array_map(function ($number) { return 'score_'.$number; }, range(1, 8)), 5);
        $invalidScores['score_1'] = 0;
        $this->actingAs($reviewer)->put(route('panels.review', $panel), $invalidScores)->assertSessionHasErrors('score_1');
        $this->assertNull(DB::table('panel_reviewers')->where('panel_id', 1)->first()->average_score);
    }

    public function test_administrator_panel_listing_loads_assigned_reviewers()
    {
        $this->actingAs($this->user(1, 1));
        $this->user(2, 2);
        $this->panel();
        DB::table('panel_reviewers')->insert(['panel_id' => 1, 'reviewer_id' => 2]);

        $response = app(PanelController::class)->index(Request::create('/panels'));
        $panel = $response->getData()['panels']->items()[0];

        $this->assertTrue($panel->relationLoaded('reviewers'));
        $this->assertSame(2, $panel->reviewers->first()->id);
        $this->assertNull($panel->reviewers->first()->pivot->average_score);
    }

    public function test_panel_cannot_have_more_than_two_reviewers()
    {
        $this->actingAs($this->user(1, 1));
        $this->user(2, 2);
        $this->user(3, 2);
        $this->user(4, 2);
        $panel = $this->panel();
        DB::table('reviewer_candidates')->insert([
            ['email' => 'user2@example.org'],
            ['email' => 'user3@example.org'],
            ['email' => 'user4@example.org'],
        ]);

        $this->withoutMiddleware(self::FORM_MIDDLEWARE)
            ->put(route('panels.assignments.update', $panel), ['reviewer_ids' => [2, 3, 4]])
            ->assertSessionHasErrors('reviewer_ids');

        $this->assertSame(0, $panel->reviewers()->count());
    }

    public function test_panel_import_matches_uppercase_emails_and_reports_rejected_rows()
    {
        $this->user(1, 1);
        $this->user(2, 2);
        $this->user(3, 2);
        $panel = $this->panel();
        $file = $this->assignmentCsv("panel_id,Revisor 1,Revisor 2\n1,USER2@EXAMPLE.ORG,USER3@EXAMPLE.ORG\n2,user2@example.org,missing@example.org\n1,user2@example.org,\n");

        try {
            $importer = app(PanelReviewerAssignmentImportService::class);
            $result = $importer->import($file);
            $this->assertSame(2, $result['added']);
            $this->assertCount(2, $result['rejected_rows']);
            $this->assertSame([2, 3], $panel->fresh()->reviewers->pluck('id')->sort()->values()->all());
            $csv = $importer->rejectedRowsCsv($result['rejected_rows']);
            $this->assertStringContainsString('missing@example.org', $csv);
            $this->assertStringContainsString('Duplicate panel_id', $csv);
        } finally {
            @unlink($file->getRealPath());
        }
    }

    public function test_panel_import_preserves_reviews_and_two_reviewer_limit()
    {
        $this->user(1, 1);
        $this->user(2, 2);
        $this->user(3, 2);
        $this->user(4, 2);
        $locked = $this->panel();
        DB::table('panels')->insert(['id' => 2, 'title' => 'Full panel', 'status' => 'Submitted']);
        $locked->reviewers()->attach(2, ['average_score' => 7]);
        Panel::findOrFail(2)->reviewers()->attach([2, 3]);
        $file = $this->assignmentCsv("panel_id,Revisor 1,Revisor 2\n1,user4@example.org,\n2,user4@example.org,\n");

        try {
            $result = app(PanelReviewerAssignmentImportService::class)->import($file);
            $this->assertSame(0, $result['added']);
            $this->assertCount(2, $result['rejected_rows']);
            $this->assertSame(1, $locked->fresh()->reviewers->count());
            $this->assertSame(2, Panel::findOrFail(2)->reviewers()->count());
        } finally {
            @unlink($file->getRealPath());
        }
    }

    public function test_panel_import_stores_downloadable_error_report_for_administrator()
    {
        Storage::fake('local');
        $this->actingAs($this->user(1, 1));
        $this->panel();
        $path = tempnam(sys_get_temp_dir(), 'panel_assign_').'.xlsx';
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray([
            ['panel_id', 'Revisor 1', 'Revisor 2'],
            [1, 'missing@example.org', null],
        ]);
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();
        $file = new UploadedFile($path, 'assignments.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        try {
            $this->withoutMiddleware(self::FORM_MIDDLEWARE)
                ->post(route('panels.assignments.import'), ['assignment_file' => $file])
                ->assertRedirect(route('panels.assignments'))
                ->assertSessionHas('panel_assignment_import_report');
            $report = session('panel_assignment_import_report');
            Storage::disk('local')->assertExists($report['path']);
            $this->get(route('panels.assignments.import_errors', $report['token']))->assertOk();
        } finally {
            @unlink($path);
        }
    }

    private function assignmentCsv(string $contents): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'panel_assign_');
        file_put_contents($path, $contents);
        return new UploadedFile($path, 'assignments.csv', 'text/csv', null, true);
    }
}
