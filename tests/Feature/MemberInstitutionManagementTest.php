<?php

namespace Tests\Feature;

use App\Http\Controllers\MemberInstitutionController;
use App\Http\Controllers\MemberIndividualController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class MemberInstitutionManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'member_institution_testing', 'database.connections.member_institution_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->boolean('is_cugh_member')->default(false);
            $table->string('cugh_membership_type')->nullable();
            $table->string('cugh_member_institution')->nullable();
            $table->timestamps();
        });
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('status');
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
        });
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
        });
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
        });
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('member_institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country')->nullable();
            $table->boolean('is_active')->default(true);
        });
        Schema::create('member_individuals', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Administrador', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'Secretaria', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'Participante', 'guard_name' => 'web'],
        ]);
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('test')],
            ['id' => 2, 'name' => 'Secretary', 'email' => 'secretary@example.com', 'password' => bcrypt('test')],
            ['id' => 3, 'name' => 'Participant', 'email' => 'participant@example.com', 'password' => bcrypt('test')],
        ]);
        DB::table('model_has_roles')->insert([
            ['role_id' => 1, 'model_type' => User::class, 'model_id' => 1],
            ['role_id' => 2, 'model_type' => User::class, 'model_id' => 2],
            ['role_id' => 3, 'model_type' => User::class, 'model_id' => 3],
        ]);
        DB::table('countries')->insert([
            ['id' => 1, 'name' => 'Peru'],
            ['id' => 2, 'name' => 'Chile'],
        ]);
        DB::table('member_institutions')->insert([
            ['id' => 1, 'name' => 'Lima Health Institute', 'country' => '1', 'is_active' => 1],
            ['id' => 2, 'name' => 'Santiago University', 'country' => '2', 'is_active' => 1],
        ]);
        DB::table('member_individuals')->insert([
            ['id' => 1, 'first_name' => 'Jane', 'last_name' => 'Doe', 'email' => 'jane@example.com', 'is_active' => 1],
            ['id' => 2, 'first_name' => 'Luis', 'last_name' => 'Perez', 'email' => 'luis@example.com', 'is_active' => 0],
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->withoutMiddleware([
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\CheckInscription::class,
            \App\Http\Middleware\EnsureStatusActive::class,
        ]);
    }

    public function test_search_finds_institution_by_name_country_or_id()
    {
        $this->actingAs(User::find(1));
        $controller = app(MemberInstitutionController::class);

        request()->merge(['search' => 'lima']);
        $this->assertSame(['Lima Health Institute'], $controller->index()->getData()['member_institutions']->pluck('name')->all());

        request()->merge(['search' => 'CHILE']);
        $this->assertSame(['Santiago University'], $controller->index()->getData()['member_institutions']->pluck('name')->all());

        request()->merge(['search' => '1']);
        $this->assertSame(['Lima Health Institute'], $controller->index()->getData()['member_institutions']->pluck('name')->all());
    }

    public function test_institution_registration_count_uses_current_institutional_membership()
    {
        DB::table('users')->insert([
            ['id' => 4, 'name' => 'Member One', 'email' => 'one@example.com', 'password' => bcrypt('test'), 'is_cugh_member' => 1, 'cugh_membership_type' => 'Institutional Member', 'cugh_member_institution' => '1'],
            ['id' => 5, 'name' => 'Member Two', 'email' => 'two@example.com', 'password' => bcrypt('test'), 'is_cugh_member' => 1, 'cugh_membership_type' => 'Institutional Member', 'cugh_member_institution' => '1'],
            ['id' => 6, 'name' => 'Former Member', 'email' => 'former@example.com', 'password' => bcrypt('test'), 'is_cugh_member' => 0, 'cugh_membership_type' => 'Institutional Member', 'cugh_member_institution' => '1'],
            ['id' => 7, 'name' => 'Individual Member', 'email' => 'individual@example.com', 'password' => bcrypt('test'), 'is_cugh_member' => 1, 'cugh_membership_type' => 'Individual Member', 'cugh_member_institution' => '1'],
            ['id' => 8, 'name' => 'Legacy Member', 'email' => 'legacy@example.com', 'password' => bcrypt('test'), 'is_cugh_member' => 1, 'cugh_membership_type' => null, 'cugh_member_institution' => '1'],
        ]);
        DB::table('inscriptions')->insert([
            ['id' => 1, 'user_id' => 4, 'status' => 'Draft'],
            ['id' => 2, 'user_id' => 5, 'status' => 'Confirmed'],
            ['id' => 3, 'user_id' => 6, 'status' => 'Confirmed'],
            ['id' => 4, 'user_id' => 7, 'status' => 'Processing'],
            ['id' => 5, 'user_id' => 8, 'status' => 'Confirmed'],
        ]);

        $this->actingAs(User::find(1));
        $counts = app(MemberInstitutionController::class)->index()->getData()['member_institutions']
            ->pluck('registrations_count', 'id')->all();

        $this->assertSame(3, (int) $counts[1]);
        $this->assertSame(0, (int) $counts[2]);

        $this->actingAs(User::find(1))
            ->get(route('member_institutions.registration_emails', 1))
            ->assertOk()
            ->assertJson(['emails' => ['legacy@example.com', 'one@example.com', 'two@example.com']]);
        $this->actingAs(User::find(3))
            ->get(route('member_institutions.registration_emails', 1))
            ->assertForbidden();
        $this->actingAs(User::find(2))
            ->get(route('member_institutions.registration_emails', 1))
            ->assertForbidden();
    }

    public function test_only_administrator_can_view_and_add_institutions()
    {
        $url = route('member_institutions.store');

        $this->actingAs(User::find(2))->get(route('member_institutions.index'))->assertForbidden();
        $this->actingAs(User::find(3))->get(route('member_institutions.index'))->assertForbidden();
        $this->actingAs(User::find(2))->post($url, ['name' => 'Secretary Institute', 'country' => 1])
            ->assertForbidden();
        $this->actingAs(User::find(3))->post($url, ['name' => 'New Institute', 'country' => 1])
            ->assertForbidden();
        $this->actingAs(User::find(1))->post($url, ['name' => 'New Institute', 'country' => 1])
            ->assertRedirect(route('member_institutions.index', ['search' => 'New Institute']));
        $this->assertDatabaseHas('member_institutions', ['name' => 'New Institute', 'country' => '1', 'is_active' => 1]);
        $this->assertDatabaseMissing('member_institutions', ['name' => 'Secretary Institute']);

        $this->post($url, ['name' => 'New Institute', 'country' => 1])
            ->assertSessionHasErrors('name');
        $this->post($url, ['name' => 'New Institute', 'country' => 2])
            ->assertRedirect(route('member_institutions.index', ['search' => 'New Institute']));

        $this->post($url, ['name' => 'Institution Without Country'])
            ->assertRedirect(route('member_institutions.index', ['search' => 'Institution Without Country']));
        $this->assertDatabaseHas('member_institutions', ['name' => 'Institution Without Country', 'country' => null]);
        $this->post($url, ['name' => 'Institution Without Country'])->assertSessionHasErrors('name');
    }

    public function test_only_administrator_can_change_institution_status()
    {
        $url = route('member_institutions_update_status');

        $this->actingAs(User::find(3))->postJson($url, ['id' => 1, 'status' => 0])->assertForbidden();
        $this->assertDatabaseHas('member_institutions', ['id' => 1, 'is_active' => 1]);

        $this->actingAs(User::find(2))->postJson($url, ['id' => 1, 'status' => 0])->assertForbidden();
        $this->assertDatabaseHas('member_institutions', ['id' => 1, 'is_active' => 1]);

        $this->actingAs(User::find(1))->postJson($url, ['id' => 1, 'status' => 0])
            ->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('member_institutions', ['id' => 1, 'is_active' => 0]);
    }

    public function test_individual_member_list_searches_names_emails_and_ids_for_administrator_only()
    {
        $controller = app(MemberIndividualController::class);
        $this->actingAs(User::find(1));

        request()->merge(['search' => 'jane DOE']);
        $this->assertSame([1], $controller->index()->getData()['members']->pluck('id')->all());

        request()->merge(['search' => 'LUIS@EXAMPLE.COM']);
        $this->assertSame([2], $controller->index()->getData()['members']->pluck('id')->all());

        request()->merge(['search' => '1']);
        $this->assertSame([1], $controller->index()->getData()['members']->pluck('id')->all());

        $this->actingAs(User::find(3))->get(route('member_individuals.index'))->assertForbidden();
        $this->actingAs(User::find(2))->get(route('member_individuals.index'))->assertForbidden();
    }

    public function test_individual_member_list_shows_registration_and_membership_use_by_email()
    {
        DB::table('users')->insert([
            ['id' => 4, 'name' => 'Jane', 'email' => 'JANE@example.com', 'password' => bcrypt('test'), 'is_cugh_member' => 1, 'cugh_membership_type' => 'Individual Member'],
            ['id' => 5, 'name' => 'Luis', 'email' => 'luis@example.com', 'password' => bcrypt('test'), 'is_cugh_member' => 0, 'cugh_membership_type' => null],
        ]);
        DB::table('inscriptions')->insert([
            ['id' => 1, 'user_id' => 4, 'status' => 'Confirmed'],
            ['id' => 2, 'user_id' => 5, 'status' => 'Draft'],
        ]);
        DB::table('member_individuals')->insert([
            'id' => 3, 'first_name' => 'No', 'last_name' => 'Registration',
            'email' => 'none@example.com', 'is_active' => 1,
        ]);

        $this->actingAs(User::find(1));
        $members = app(MemberIndividualController::class)->index()->getData()['members']
            ->getCollection()->keyBy('email');

        $this->assertTrue($members['jane@example.com']->has_registration);
        $this->assertTrue($members['jane@example.com']->used_individual_membership);
        $this->assertTrue($members['luis@example.com']->has_registration);
        $this->assertFalse($members['luis@example.com']->used_individual_membership);
        $this->assertFalse($members['none@example.com']->has_registration);
    }

    public function test_only_administrator_can_add_individual_member_and_email_is_unique_regardless_of_case()
    {
        $url = route('member_individuals.store');
        $data = ['first_name' => 'Maria', 'last_name' => 'Lopez', 'email' => 'MARIA@EXAMPLE.COM'];

        $this->actingAs(User::find(3))->post($url, $data)->assertForbidden();
        $this->actingAs(User::find(2))->post($url, $data)->assertForbidden();
        $this->actingAs(User::find(1))->post($url, $data)
            ->assertRedirect(route('member_individuals.index', ['search' => 'maria@example.com']));
        $this->assertDatabaseHas('member_individuals', [
            'first_name' => 'Maria', 'last_name' => 'Lopez',
            'email' => 'maria@example.com', 'is_active' => 1,
        ]);

        $this->actingAs(User::find(1))->post($url, ['email' => 'maria@example.com'])
            ->assertSessionHasErrors('email');
        DB::table('member_individuals')->where('id', 1)->update(['email' => 'JANE@EXAMPLE.COM']);
        $this->post($url, ['email' => 'jane@example.com'])->assertSessionHasErrors('email');
        $this->post($url, ['email' => 'another@example.com'])
            ->assertRedirect(route('member_individuals.index', ['search' => 'another@example.com']));
        $this->assertDatabaseHas('member_individuals', [
            'email' => 'another@example.com', 'first_name' => null, 'last_name' => null,
        ]);
    }
}
