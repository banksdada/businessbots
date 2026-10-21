<?php

namespace Tests\Feature;

use App\Models\LoginToken;
use App\Models\User;
use App\Services\Crm\CrmCaptureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrmCaptureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.crm.webhook_url', 'https://aios.test/webhook/crm');
        config()->set('services.crm.webhook_secret', 'test-secret');
    }

    public function test_clicking_the_magic_link_pushes_the_user_to_the_crm(): void
    {
        Http::fake(['aios.test/*' => Http::response(['ok' => true], 200)]);

        $user = User::factory()->create(['email' => 'Verifier@Example.com', 'name' => 'Ada Lovelace']);
        $token = LoginToken::issueFor($user);

        $this->get(route('login.verify', $token->token))->assertRedirect(route('onboarding'));

        Http::assertSent(function ($request) {
            $body = $request->body();

            $expected = 'sha256=' . hash_hmac(
                'sha256',
                $request->header('X-AIOS-Timestamp')[0] . '.' . $body,
                'test-secret'
            );

            $decoded = json_decode($body, true);

            return $request->url() === 'https://aios.test/webhook/crm'
                && $decoded['email'] === 'verifier@example.com'
                && $decoded['name'] === 'Ada Lovelace'
                && $decoded['stage'] === 'Lead'
                && $request->header('X-AIOS-Signature')[0] === $expected;
        });
    }

    public function test_a_crm_failure_does_not_block_the_login(): void
    {
        Http::fake(['aios.test/*' => Http::response('nope', 500)]);
        Log::shouldReceive('warning')->atLeast()->once();

        $user = User::factory()->create();
        $token = LoginToken::issueFor($user);

        $this->get(route('login.verify', $token->token))->assertRedirect(route('onboarding'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_a_network_error_does_not_block_the_login(): void
    {
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('timed out'));
        Log::shouldReceive('warning')->atLeast()->once();

        $user = User::factory()->create();
        $token = LoginToken::issueFor($user);

        $this->get(route('login.verify', $token->token))->assertRedirect(route('onboarding'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_capture_is_skipped_when_no_secret_is_configured(): void
    {
        config()->set('services.crm.webhook_secret', '');
        Http::fake();
        Log::shouldReceive('warning')->atLeast()->once();

        $result = app(CrmCaptureService::class)->capture(User::factory()->create());

        $this->assertFalse($result);
        Http::assertNothingSent();
    }

    public function test_an_invalid_token_does_not_touch_the_crm(): void
    {
        Http::fake();

        $this->get(route('login.verify', 'not-a-real-token'))->assertRedirect(route('login'));

        Http::assertNothingSent();
    }
}
