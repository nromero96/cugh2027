<?php

namespace Tests\Feature;

use App\Models\Inscription;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
                'other_sector', 'other_area_of_work', 'other_how_did_you_hear_about',
                'other_why_attending', 'ability_to_present_work', 'other_how_is_your_attendance_funded',
                'other_your_areas_of_focus_in_global_health', 'other_obstacles_to_attending_cughs_conferences',
                'receive_news_and_updates', 'contact_info', 'oral_poster_abstract_presenter',
                'panel_presenter_moderator',
            ] as $field) {
                $table->string($field)->nullable();
            }
            foreach ([
                'sector', 'area_of_work', 'how_did_you_hear_about', 'why_attending',
                'how_is_your_attendance_funded', 'your_areas_of_focus_in_global_health',
                'obstacles_to_attending_cughs_conferences',
            ] as $field) {
                $table->text($field)->nullable();
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
                'special_code', 'special_code_email', 'invoice', 'invoice_type', 'invoice_type_document',
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
            $table->string('membership_type')->default('all');
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

    public function test_coupon_amount_controls_paid_or_free_registration_even_when_nonzero()
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\CheckInscription::class,
            \App\Http\Middleware\EnsureStatusActive::class,
        ]);
        Schema::create('special_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->decimal('amount', 8, 2);
            $table->string('status');
            $table->string('payment_required');
            $table->integer('quantity');
            $table->date('expiration');
            $table->string('usage_rule');
            $table->string('allowed_email')->nullable();
            $table->timestamps();
        });
        Schema::create('special_code_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('special_code_id');
            $table->unsignedBigInteger('category_inscription_id');
        });
        Schema::create('special_code_allowed_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('special_code_id');
            $table->string('email');
        });
        DB::table('special_codes')->insert([
            'id' => 1, 'code' => 'PROMO', 'amount' => 320, 'status' => 'Activo',
            'payment_required' => 'Si', 'quantity' => 2, 'usage_rule' => 'per_email',
            'expiration' => now('America/Lima')->addDay()->toDateString(),
            'allowed_email' => 'owner@example.com',
        ]);
        DB::table('special_code_categories')->insert(['special_code_id' => 1, 'category_inscription_id' => 1]);
        DB::table('inscriptions')->where('id', 7)->update(['status' => 'Draft', 'category_inscription_id' => 1]);
        $data = [
            'name' => 'Owner', 'email' => 'owner@example.com', 'is_cugh_member' => '0',
            'country' => 1, 'category_inscription_id' => 1, 'price_category' => '50.00',
            'invoice' => 'no', 'invoice_type' => 'Boleta', 'payment_method' => 'Credit/Debit Card',
            'specialcode' => 'promo',
        ];
        $this->actingAs(User::find(1))->put(route('inscriptions.update', 7), $data)
            ->assertRedirect(route('inscriptions.edit', 7));
        $this->assertDatabaseHas('inscriptions', ['id' => 7, 'price_category' => 320, 'total' => 320, 'special_code' => 'PROMO']);

        DB::table('special_codes')->where('id', 1)->update(['payment_required' => 'No']);
        $noPaymentData = array_diff_key($data, array_flip(['invoice', 'invoice_type']));
        $noPaymentData['payment_method'] = 'none';
        $noPaymentData['invoice_type_document'] = 'invalid billing value';
        $this->put(route('inscriptions.update', 7), $noPaymentData)
            ->assertRedirect(route('inscriptions.edit', 7));
        $this->assertDatabaseHas('inscriptions', [
            'id' => 7, 'price_category' => 320, 'total' => 320,
            'payment_method' => 'none', 'special_code' => 'PROMO',
            'special_code_email' => 'owner@example.com',
            'invoice' => 'no', 'invoice_type' => null,
            'invoice_type_document' => null,
        ]);

        $this->actingAs(User::find(2))->post(route('specialcodes.validatespecialcode'), [
            'code' => 'PROMO', 'category_inscription_id' => 1, 'email' => 'other@example.com',
        ])->assertOk()->assertJson(['success' => false]);
        $this->post(route('specialcodes.validatespecialcode'), [
            'code' => 'PROMO', 'category_inscription_id' => 1, 'email' => 'owner@example.com',
        ])->assertOk()->assertJson(['success' => true, 'price' => 320, 'payment_required' => 'No']);
    }

    public function test_no_payment_special_code_sets_processing_and_sends_pre_registration_email()
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\CheckInscription::class,
            \App\Http\Middleware\EnsureStatusActive::class,
        ]);
        Mail::fake();

        Schema::create('special_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->decimal('amount', 8, 2);
            $table->string('status');
            $table->string('payment_required');
            $table->integer('quantity');
            $table->date('expiration');
            $table->string('usage_rule');
            $table->string('allowed_email')->nullable();
            $table->timestamps();
        });
        Schema::create('special_code_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('special_code_id');
            $table->unsignedBigInteger('category_inscription_id');
        });
        Schema::create('special_code_allowed_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('special_code_id');
            $table->string('email');
        });
        DB::table('special_codes')->insert([
            'id' => 1, 'code' => 'PROMO', 'amount' => 120, 'status' => 'Activo',
            'payment_required' => 'No', 'quantity' => 1, 'usage_rule' => 'single_use',
            'expiration' => now('America/Lima')->addDay()->toDateString(),
            'allowed_email' => 'owner@example.com',
        ]);
        DB::table('special_code_categories')->insert(['special_code_id' => 1, 'category_inscription_id' => 1]);
        DB::table('inscriptions')->where('id', 7)->update(['status' => 'Draft']);

        $data = [
            'action' => 'register', 'salutation' => 'Ms', 'name' => 'Owner',
            'second_lastname' => 'Tester', 'degrees' => 'Other', 'is_cugh_member' => '0',
            'job_title' => 'Researcher', 'email' => 'owner@example.com',
            'document_type' => 'Passport', 'document_number' => '12345',
            'nationality' => 'Test Country', 'gender' => 'Female', 'occupation' => 'Researcher',
            'workplace' => 'Test University', 'address' => 'Test Address', 'city' => 'Lima',
            'state' => 'Lima', 'country' => 1, 'phone_code' => '+51', 'phone_number' => '123456789',
            'solapin_name' => 'Owner', 'solapin_lastname' => 'Tester',
            'sector' => ['Academic'], 'area_of_work' => ['Research'],
            'how_did_you_hear_about' => ['Email'], 'why_attending' => ['Networking'],
            'how_is_your_attendance_funded' => ['Self-funded'],
            'your_areas_of_focus_in_global_health' => ['Health'],
            'obstacles_to_attending_cughs_conferences' => ['None'],
            'receive_news_and_updates' => 'yes', 'contact_info' => 'yes',
            'oral_poster_abstract_presenter' => 'no', 'panel_presenter_moderator' => 'no',
            'category_inscription_id' => 1, 'payment_method' => 'none', 'specialcode' => 'promo',
        ];

        $this->actingAs(User::find(2))->post(route('inscriptions.storemyinscription'), $data)
            ->assertRedirect(route('inscriptions.index'));
        $this->assertDatabaseHas('inscriptions', [
            'id' => 7, 'status' => 'Processing', 'payment_method' => 'none',
            'price_category' => 120, 'total' => 120, 'special_code' => 'PROMO',
        ]);
        Mail::assertSent(\App\Mail\InscriptionCreated::class, function ($mail) {
            return $mail->hasTo('owner@example.com') && $mail->datainscription->special_code === 'PROMO';
        });
        Mail::assertNotSent(\App\Mail\InscriptionConfirmed::class);
    }
}
