<?php

namespace Tests\Feature;

use App\Models\Inscription;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class InscriptionPrivateNoteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'private_note_testing', 'database.connections.private_note_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->unsignedBigInteger('user_id');
            $table->string('status');
            $table->text('private_note')->nullable();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Administrador', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'Participante', 'guard_name' => 'web'],
        ]);
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('test')],
            ['id' => 2, 'name' => 'Owner', 'email' => 'owner@example.com', 'password' => bcrypt('test')],
        ]);
        DB::table('model_has_roles')->insert([
            ['role_id' => 1, 'model_type' => User::class, 'model_id' => 1],
            ['role_id' => 2, 'model_type' => User::class, 'model_id' => 2],
        ]);
        DB::table('inscriptions')->insert([
            'id' => 7, 'token' => 'test-token', 'user_id' => 2, 'status' => 'Confirmed',
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_only_administrator_can_update_or_clear_a_private_note_even_when_confirmed()
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\CheckInscription::class,
            \App\Http\Middleware\EnsureStatusActive::class,
        ]);

        $url = route('inscriptions.private-note.update', 7);
        $this->actingAs(User::find(2))->put($url, ['private_note' => 'Participant edit'])->assertForbidden();
        $this->assertNull(Inscription::find(7)->private_note);

        $this->actingAs(User::find(1))->put($url, ['private_note' => 'Call the participant.'])
            ->assertRedirect(route('inscriptions.show', 7));
        $this->assertSame('Call the participant.', Inscription::find(7)->private_note);
        $this->assertArrayNotHasKey('private_note', Inscription::find(7)->toArray());

        $this->put($url, ['private_note' => str_repeat('x', 5001)])->assertSessionHasErrors('private_note');
        $this->assertSame('Call the participant.', Inscription::find(7)->private_note);

        $this->put($url, ['private_note' => ''])->assertRedirect(route('inscriptions.show', 7));
        $this->assertNull(Inscription::find(7)->private_note);
    }
}
