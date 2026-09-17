<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SubmissionClosureTest extends TestCase
{
    private const MESSAGE = 'ABSTRACT SUBMISSION IS NOW CLOSED';

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'submission_testing', 'database.connections.submission_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
    }

    public function test_panel_submission_form_is_open()
    {
        $this->get(route('panels.formonline'))
            ->assertOk()
            ->assertSee('id="panelForm"', false)
            ->assertDontSeeText(self::MESSAGE);
    }

    public function test_panel_submission_endpoint_accepts_requests_and_validates_them()
    {
        $this->from(route('panels.formonline'))->withoutMiddleware()
            ->post(route('panels.storeonline'), ['contact_email' => 'test@example.com'])
            ->assertRedirect(route('panels.formonline'))
            ->assertSessionHasErrors(['language', 'subthemes', 'title']);
    }

    public function test_abstract_creation_form_displays_closed_message()
    {
        $this->withoutMiddleware()
            ->get(route('abstract_posts.create'))
            ->assertOk()
            ->assertSeeText(self::MESSAGE);
    }

    public function test_abstract_store_endpoint_rejects_new_records()
    {
        $this->withoutMiddleware()
            ->post(route('abstract_posts.store'), ['action' => 'draft'])
            ->assertStatus(410)
            ->assertSeeText(self::MESSAGE);
    }
}
