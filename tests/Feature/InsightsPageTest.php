<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

class InsightsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_insights_index_lists_published_posts(): void
    {
        $this->get('/insights')
            ->assertOk()
            ->assertSee('Insights')
            ->assertSee("You don't need AI. You need the problem gone.")
            ->assertSee('two-minute-test');
    }

    public function test_insight_post_renders_without_a_500(): void
    {
        // Regression test: the single-post view used to pass the title as
        // :title="{{ $post['title'] }}" which threw a Blade render error.
        $this->get('/insights/you-dont-need-ai')
            ->assertOk()
            ->assertSee("You don't need AI. You need the problem gone.");
    }

    public function test_unknown_insight_slug_is_a_404(): void
    {
        $this->get('/insights/does-not-exist')->assertNotFound();
    }
}