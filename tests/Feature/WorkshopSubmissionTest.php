<?php

namespace Tests\Feature;

use App\Models\Workshop;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class WorkshopSubmissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Isolated in-memory connection: never migrate or reset the production database.
        config(['database.default' => 'workshop_testing', 'database.connections.workshop_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            foreach ((new Workshop())->getFillable() as $field) {
                $table->text($field)->nullable();
            }
            $table->timestamps();
        });
        Storage::fake('public');
    }

    private function payload(): array
    {
        return [
            'submission_token' => Crypt::encryptString((string) Str::uuid()),
            'lead_name' => 'Test Lead', 'lead_institution' => 'Test Institution',
            'lead_title' => 'Professor', 'lead_email' => 'lead@example.com',
            'lead_phone' => '+5112345678', 'lead_cell' => '+51987654321',
            'workshop_title' => 'Workshop test', 'workshop_desc' => 'Workshop description',
            'workshop_objectives' => 'Learning objectives', 'time_slot' => 'Morning, 9am-12pm',
            'day_length' => 'Half Day', 'room_setup' => 'theater', 'attendees' => 20,
            'payment_lead_same' => 'Yes', 'terms' => 'on', 'place_date' => '2026-09-03',
            'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aE1sAAAAASUVORK5CYII=',
        ];
    }

    public function test_form_renders_submission_token_and_restores_old_fields()
    {
        $this->withSession(['_old_input' => ['lead_name' => 'Preserved Name', 'payment_lead_same' => 'No']])
            ->get(route('workshops.registerworkshop'))
            ->assertOk()->assertSee('submission_token')->assertSee('Preserved Name');
    }

    public function test_valid_submission_is_saved_and_repeated_token_does_not_duplicate_it()
    {
        $payload = $this->payload();
        $this->post(route('workshops.storeworkshop'), $payload)->assertSessionHasNoErrors()->assertRedirect(route('workshops.registerworkshop'));
        $this->post(route('workshops.storeworkshop'), $payload)->assertSessionHasNoErrors();
        $this->assertSame(1, Workshop::count());
        Storage::disk('public')->assertExists('uploads/workshops/'.Workshop::first()->signature_path);
    }

    public function test_alternative_payment_contact_is_required_and_input_is_preserved()
    {
        $payload = array_merge($this->payload(), ['payment_lead_same' => 'No', 'payment_email' => 'invalid']);
        $this->from(route('workshops.registerworkshop'))->post(route('workshops.storeworkshop'), $payload)
            ->assertRedirect(route('workshops.registerworkshop'))
            ->assertSessionHasErrors(['payment_name', 'payment_email', 'payment_phone'])
            ->assertSessionHas('_old_input.lead_name', 'Test Lead')
            ->assertSessionMissing('_old_input.signature');
        $this->assertSame(0, Workshop::count());
    }

    public function test_invalid_signature_terms_options_and_word_limit_are_rejected()
    {
        $payload = array_merge($this->payload(), [
            'signature' => 'data:image/png;base64,'.base64_encode('not an image'),
            'terms' => 'no', 'room_setup' => 'unknown',
            'workshop_desc' => implode(' ', array_fill(0, 201, 'word')),
        ]);
        $this->post(route('workshops.storeworkshop'), $payload)
            ->assertSessionHasErrors(['signature', 'terms', 'room_setup', 'workshop_desc']);
        $this->assertSame([], Storage::disk('public')->allFiles());
        $this->assertSame(0, Workshop::count());
    }

    public function test_exactly_200_words_and_valid_alternative_contact_are_accepted()
    {
        $payload = array_merge($this->payload(), [
            'payment_lead_same' => 'No', 'payment_name' => 'Payment Lead',
            'payment_institution' => 'Payment Institution', 'payment_title' => 'Manager',
            'payment_email' => 'payment@example.com', 'payment_phone' => '+5112345',
            'payment_cell' => '+5198765',
            'workshop_desc' => implode(' ', array_fill(0, 200, 'word')),
        ]);
        $this->post(route('workshops.storeworkshop'), $payload)->assertSessionHasNoErrors();
        $this->assertSame('payment@example.com', Workshop::first()->payment_email);
    }

    public function test_inconsistent_duration_and_modified_token_are_rejected()
    {
        $this->post(route('workshops.storeworkshop'), array_merge($this->payload(), ['day_length' => 'Full Day']))
            ->assertSessionHasErrors('day_length');
        $this->post(route('workshops.storeworkshop'), array_merge($this->payload(), ['submission_token' => 'tampered']))
            ->assertSessionHasErrors('submission_token');
        $this->assertSame(0, Workshop::count());
    }

    public function test_database_failure_removes_signature_and_returns_a_friendly_error()
    {
        Workshop::creating(function () { throw new \RuntimeException('Simulated database failure'); });
        try {
            $this->post(route('workshops.storeworkshop'), $this->payload())
                ->assertSessionHasErrors('submission')
                ->assertSessionHas('_old_input.lead_name', 'Test Lead');
            $this->assertSame(0, Workshop::count());
            $this->assertSame([], Storage::disk('public')->allFiles());
        } finally {
            Workshop::flushEventListeners();
        }
    }
}
