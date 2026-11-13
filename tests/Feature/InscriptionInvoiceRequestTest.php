<?php

namespace Tests\Feature;

use App\Models\Inscription;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class InscriptionInvoiceRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'invoice_request_testing', 'database.connections.invoice_request_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        config([
            'services.invoicing.url_request_invoice' => 'https://example.test/invoice',
            'services.invoicing.api_token' => 'test-bearer-token',
        ]);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lastname')->nullable();
            $table->string('second_lastname')->nullable();
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
            $table->string('status_compr')->default('Ninguna');
            $table->timestamps();
        });
        Schema::create('statusnotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inscription_id');
            $table->string('action');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Administrador', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'Secretaria', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'Participante', 'guard_name' => 'web'],
        ]);
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Admin', 'lastname' => null, 'email' => 'admin@example.test', 'password' => bcrypt('test')],
            ['id' => 2, 'name' => 'Secretary', 'lastname' => null, 'email' => 'secretary@example.test', 'password' => bcrypt('test')],
            ['id' => 3, 'name' => 'Alvaro', 'lastname' => 'Bernal', 'email' => 'participant@example.test', 'password' => bcrypt('test')],
        ]);
        DB::table('model_has_roles')->insert([
            ['role_id' => 1, 'model_type' => User::class, 'model_id' => 1],
            ['role_id' => 2, 'model_type' => User::class, 'model_id' => 2],
            ['role_id' => 3, 'model_type' => User::class, 'model_id' => 3],
        ]);
        DB::table('inscriptions')->insert([
            'id' => 7,
            'token' => '230df0ed-2661-41f7-a463-579ef8f75ffd',
            'user_id' => 3,
            'status' => 'Confirmed',
            'status_compr' => 'Ninguna',
            'created_at' => '2026-09-10 10:00:00',
            'updated_at' => '2026-09-20 10:00:00',
        ]);
        DB::table('statusnotes')->insert([
            'inscription_id' => 7,
            'action' => "Changed from 'Paid' to 'Confirmed'",
            'note' => 'Confirmed',
            'user_id' => 1,
            'created_at' => '2026-09-17 10:00:00',
            'updated_at' => '2026-09-17 10:00:00',
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->withoutMiddleware([
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\CheckInscription::class,
            \App\Http\Middleware\EnsureStatusActive::class,
        ]);
    }

    public function test_confirmed_invoice_request_sends_expected_payload_and_cannot_be_sent_twice()
    {
        Http::fake(['https://example.test/invoice' => Http::response(['success' => true], 202)]);

        $this->actingAs(User::find(1))->post(route('inscriptions.requestcomprobante', 7))
            ->assertRedirect(route('inscriptions.show', 7))
            ->assertSessionHas('success');

        $this->assertSame('Solicitado', Inscription::find(7)->status_compr);
        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request->url() === 'https://example.test/invoice'
                && $request->hasHeader('Authorization', 'Bearer test-bearer-token')
                && $request->data() === [
                    'uuid' => '230df0ed-2661-41f7-a463-579ef8f75ffd',
                    'usuario_id' => '3',
                    'usuario_nombre' => 'Alvaro Bernal',
                    'estado_inscripcion' => 'AUTORIZADO_EMISION',
                    'fecha_autorizado' => '2026-09-17',
                ];
        });

        $this->post(route('inscriptions.requestcomprobante', 7))->assertSessionHas('error');
        Http::assertSentCount(1);
    }

    public function test_only_staff_can_request_invoice_for_confirmed_registration()
    {
        Http::fake();
        $url = route('inscriptions.requestcomprobante', 7);

        $this->actingAs(User::find(3))->post($url)->assertForbidden();
        DB::table('inscriptions')->where('id', 7)->update(['status' => 'Draft']);
        $this->actingAs(User::find(2))->post($url)->assertSessionHas('error');

        $this->assertSame('Ninguna', Inscription::find(7)->status_compr);
        Http::assertNothingSent();
    }

    public function test_rejected_api_request_can_be_retried_without_marking_it_requested()
    {
        Http::fake(['https://example.test/invoice' => Http::response(['error' => 'Invalid request'], 400)]);

        $this->actingAs(User::find(1))->post(route('inscriptions.requestcomprobante', 7))
            ->assertSessionHas('error');

        $this->assertSame('Ninguna', Inscription::find(7)->status_compr);
        Http::assertSentCount(1);
    }

    public function test_uncertain_network_outcome_remains_claimed_to_avoid_duplicate_issuance()
    {
        $attempts = 0;
        Http::fake(function () use (&$attempts) {
            $attempts++;
            throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
        });

        $this->actingAs(User::find(1))->post(route('inscriptions.requestcomprobante', 7))
            ->assertSessionHas('error');

        $this->assertSame('Procesando', Inscription::find(7)->status_compr);
        $this->post(route('inscriptions.requestcomprobante', 7))->assertSessionHas('error');
        $this->assertSame(1, $attempts);
    }
}
