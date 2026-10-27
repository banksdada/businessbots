<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_lists_the_public_pages(): void
    {
        config(['features.social' => false]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $response->assertSee('https://nowsorted.co.uk/', false);
        $response->assertSee('https://nowsorted.co.uk/pricing', false);
        $response->assertSee('https://nowsorted.co.uk/insights/you-dont-need-ai', false);
        $response->assertSee('https://nowsorted.co.uk/ai-audit/', false);
        $response->assertSee('https://nowsorted.co.uk/career/', false);
        $response->assertSee('https://nowsorted.co.uk/consult/', false);
        $response->assertDontSee('https://nowsorted.co.uk/live-proof', false);
    }

    public function test_sitemap_lists_live_proof_only_when_social_is_on(): void
    {
        config(['features.social' => true]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('https://nowsorted.co.uk/live-proof', false);
    }

    public function test_sitemap_omits_the_unlinked_usage_guide(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertDontSee('https://nowsorted.co.uk/getting-started', false);
    }

    public function test_home_page_has_canonical_and_social_meta(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="https://nowsorted.co.uk/"', false)
            ->assertSee('<meta name="description"', false)
            ->assertSee('<meta property="og:title"', false)
            ->assertSee('<meta property="og:image" content="https://nowsorted.co.uk/images/og-default.png"', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('<meta name="twitter:image" content="https://nowsorted.co.uk/images/og-default.png"', false);
    }

    public function test_canonical_url_drops_query_strings(): void
    {
        $this->get('/contact?email=test@example.com')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="https://nowsorted.co.uk/contact"', false);
    }

    public function test_default_social_image_exists_at_the_expected_size(): void
    {
        $path = public_path('images/og-default.png');

        $this->assertFileExists($path);
        $this->assertSame([1200, 630], array_slice(getimagesize($path), 0, 2));
    }

    public function test_static_microsites_have_canonical_and_social_meta(): void
    {
        foreach (['ai-audit/index.html', 'career/index.html', 'consult/index.html'] as $file) {
            $html = file_get_contents(public_path($file));

            $this->assertStringContainsString('rel="canonical"', $html, $file);
            $this->assertStringContainsString('property="og:title"', $html, $file);
            $this->assertStringContainsString('property="og:image"', $html, $file);
            $this->assertStringContainsString('name="twitter:card"', $html, $file);
        }
    }

    public function test_robots_points_at_the_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Sitemap: https://nowsorted.co.uk/sitemap.xml', $robots);
        $this->assertStringContainsString('Disallow: /dashboard', $robots);
    }
}
