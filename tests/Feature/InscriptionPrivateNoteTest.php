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
            foreach ([
                'salutation', 'lastname', 'second_lastname', 'degrees', 'other_degrees',
                'is_cugh_member', 'cugh_membership_type', 'cugh_member_institution', 'job_title',
                'cc_email', 'document_type', 'document_number', 'nationality', 'gender',
                'occupation', 'occupation_other', 'workplace', 'address', 'city', 'state', 'country',
                'work_phone_code', 'work_phone_code_city', 'work_phone_number', 'phone_code',
                'phone_number', 'whatsapp_code', 'whatsapp_number', 'solapin_name', 'solapin_lastname',
            ] as $field) {
                $table->string($field)->nullable();
            }
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
            $table->unsignedBigInteger('category_inscription_id')->nullable();
            $table->decimal('price_category', 8, 2)->nullable();
            $table->decimal('total', 8, 2)->nullable();
            foreach ([
                'special_code', 'invoice', 'invoice_type', 'invoice_type_document',
                'invoice_ruc', 'invoice_social_reason', 'invoice_address', 'payment_method',
                'voucher_file', 'document_file',
            ] as $field) {
                $table->string($field)->nullable();
            }
            $table->timestamps();
        });
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('price_type');
        });
        Schema::create('category_inscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->decimal('price', 8, 2);
            $table->decimal('price_low', 8, 2)->nullable();
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
        DB::table('countries')->insert(['id' => 1, 'name' => 'Test Country', 'price_type' => 'High Income']);
        DB::table('category_inscriptions')->insert([
            'id' => 1, 'name' => 'Regular', 'status' => 'active', 'price' => 100, 'price_low' => 50,
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

    public function test_only_administrator_can_open_edit_and_submit_registration_updates()
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\CheckInscription::class,
            \App\Http\Middleware\EnsureStatusActive::class,
        ]);

        $editUrl = route('inscriptions.edit', 7);
        $updateUrl = route('inscriptions.update', 7);
        $participant = User::find(2);
        $administrator = User::find(1);

        $this->actingAs($participant)->get($editUrl)->assertForbidden();
        $this->put($updateUrl, ['name' => 'Unauthorized change'])->assertForbidden();
        $this->patch($updateUrl, ['name' => 'Unauthorized change'])->assertForbidden();
        $this->assertSame('Owner', $participant->fresh()->name);

        $this->actingAs($administrator)->get($editUrl)
            ->assertRedirect(route('inscriptions.show', 7));
        $this->put($updateUrl, ['name' => 'Blocked for confirmed'])
            ->assertRedirect(route('inscriptions.show', 7));
        $this->assertSame('Owner', $participant->fresh()->name);

        DB::table('inscriptions')->where('id', 7)->update(['status' => 'Draft']);
        $this->put($updateUrl, [])->assertSessionHasErrors('name');
        $this->assertSame('Owner', $participant->fresh()->name);
    }

    public function test_administrator_adjusts_only_this_registrations_fee_and_total()
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\CheckInscription::class,
            \App\Http\Middleware\EnsureStatusActive::class,
        ]);

        DB::table('users')->where('id', 2)->update(['country' => 1]);
        DB::table('inscriptions')->where('id', 7)->update([
            'status' => 'Draft',
            'category_inscription_id' => 1,
            'price_category' => 100,
            'total' => 100,
            'payment_method' => 'Credit/Debit Card',
        ]);

        $url = route('inscriptions.update', 7);
        $data = [
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'is_cugh_member' => '0',
            'country' => 1,
            'category_inscription_id' => 1,
            'price_category' => '75.25',
            'invoice' => 'no',
            'invoice_type' => 'Boleta',
            'payment_method' => 'Credit/Debit Card',
        ];

        $this->actingAs(User::find(2))->put($url, $data)->assertForbidden();
        $this->actingAs(User::find(1))->put($url, array_merge($data, ['price_category' => '75.251']))
            ->assertSessionHasErrors('price_category');
        $this->assertDatabaseHas('inscriptions', ['id' => 7, 'price_category' => 100, 'total' => 100]);

        $this->put($url, $data)->assertRedirect(route('inscriptions.edit', 7));
        $this->assertDatabaseHas('inscriptions', ['id' => 7, 'price_category' => 75.25, 'total' => 75.25]);
        $this->assertDatabaseHas('category_inscriptions', ['id' => 1, 'price' => 100]);

        $this->put($url, array_merge($data, ['price_category' => '0.00', 'payment_method' => 'none']))
            ->assertRedirect(route('inscriptions.edit', 7));
        $this->assertDatabaseHas('inscriptions', ['id' => 7, 'price_category' => 0, 'total' => 0]);
    }
}
