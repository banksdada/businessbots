<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Crm\CrmCaptureService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CrmCaptureTest extends TestCase
{
    public function test_it_signs_the_exact_body_the_aios_receives(): void
    {
        config([
            'services.crm.webhook_url' => 'https://aios.test/webhook/crm',
            'services.crm.webhook_secret' => 'shared-secret',
            'services.crm.timeout_seconds' => 5,
        ]);

        Http::fake();

        $user = new User(['name' => 'Eden Life', 'email' => 'EdenLife@Example.com']);

        $this->assertTrue(app(CrmCaptureService::class)->capture($user));

        Http::assertSent(function (Request $request) {
            $timestamp = $request->header('X-AIOS-Timestamp')[0] ?? '';
            $signature = $request->header('X-AIOS-Signature')[0] ?? '';
            $expected = 'sha256=' . hash_hmac(
                'sha256',
                $timestamp . '.' . $request->body(),
                'shared-secret'
            );

            $payload = json_decode($request->body(), true);

            return $request->url() === 'https://aios.test/webhook/crm'
                && $signature === $expected
                && $payload === [
                    'name' => 'Eden Life',
                    'email' => 'edenlife@example.com',
                    'stage' => 'Lead',
                ];
        });
    }

    public function test_it_sends_nothing_without_a_secret(): void
    {
        config([
            'services.crm.webhook_url' => 'https://aios.test/webhook/crm',
            'services.crm.webhook_secret' => null,
        ]);

        Http::fake();

        $user = new User(['name' => 'Eden Life', 'email' => 'edenlife@example.com']);

        $this->assertFalse(app(CrmCaptureService::class)->capture($user));

        Http::assertNothingSent();
    }

    public function test_a_failed_capture_never_throws(): void
    {
        config([
            'services.crm.webhook_url' => 'https://aios.test/webhook/crm',
            'services.crm.webhook_secret' => 'shared-secret',
        ]);

        Http::fake([
            'aios.test/*' => Http::response(['ok' => false], 500),
        ]);

        $user = new User(['name' => 'Eden Life', 'email' => 'edenlife@example.com']);

        $this->assertFalse(app(CrmCaptureService::class)->capture($user));
    }
}
