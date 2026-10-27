<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalEmailTest extends TestCase
{
    public function test_legal_pages_use_nowsorted_contact_addresses(): void
    {
        foreach (['/terms', '/privacy', '/cookies'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertDontSee('automation.baseuse.xyz')
                ->assertDontSee('businessbots.com');
        }

        $this->get('/terms')->assertSee('legal@nowsorted.co.uk');
        $this->get('/privacy')
            ->assertSee('legal@nowsorted.co.uk')
            ->assertSee('privacy@nowsorted.co.uk');
        $this->get('/cookies')->assertSee('privacy@nowsorted.co.uk');
    }
}
