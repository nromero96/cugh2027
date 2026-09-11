<?php

namespace Tests\Feature;

use Tests\TestCase;

class SubmissionClosureTest extends TestCase
{
    private const MESSAGE = 'ABSTRACT AND PANEL SUBMISSION IS NOW CLOSED';

    public function test_panel_submission_form_displays_closed_message()
    {
        $this->get(route('panels.formonline'))
            ->assertOk()
            ->assertSeeText(self::MESSAGE);
    }

    public function test_panel_submission_endpoint_rejects_new_records()
    {
        $this->withoutMiddleware()
            ->post(route('panels.storeonline'), ['contact_email' => 'test@example.com'])
            ->assertStatus(410)
            ->assertSeeText(self::MESSAGE);
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
