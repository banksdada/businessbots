<?php

namespace Tests\Feature;

use App\Filament\Ops\Resources\ProblemRequestResource\Pages\EditProblemRequest;
use App\Livewire\Problems\SubmitProblem;
use App\Mail\AdviceRequestFailedMail;
use App\Mail\DraftReadyMail;
use App\Mail\ReportReadyMail;
use App\Models\AutomationJob;
use App\Models\Business;
use App\Models\ProblemRequest;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

class ClientAdviceFlowTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'test-worker-token';

    private Business $business;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'billing.required' => false,
            'services.pyrunner.worker_token' => self::TOKEN,
            'services.pyrunner.advice_webhook' => 'https://pyrunner.test/webhook/abc/',
            'portal.owner_email' => 'owner@example.com',
        ]);

        Mail::fake();
        Http::fake(['pyrunner.test/*' => Http::response(['status' => 'queued'])]);

        $this->business = Business::factory()->active()->create(['name' => 'Bright Care']);
        $this->business->businessVertical()->create(['vertical_type' => 'care']);
    }

    private function submitProblem(?User $user = null): ProblemRequest
    {
        Livewire::actingAs($user ?? $this->business->owner)
            ->test(SubmitProblem::class)
            ->set('title', 'Rotas take a whole day')
            ->set('description', 'Every week our manager spends a full day building staff rotas by hand in Excel.')
            ->set('already_tried', 'A shared spreadsheet')
            ->set('staff_count', 25)
            ->set('urgency', 'high')
            ->set('help_wanted', 'advice')
            ->call('submit')
            ->assertHasNoErrors();

        return ProblemRequest::latest('id')->firstOrFail();
    }

    private function worker(string $method, string $uri, array $data = [])
    {
        return $this->withToken(self::TOKEN)->json($method, $uri, $data);
    }

    public function test_job_api_rejects_missing_or_wrong_token(): void
    {
        $this->postJson('/api/pyrunner/jobs/claim', ['type' => 'client_advice'])->assertUnauthorized();
        $this->withToken('wrong')->postJson('/api/pyrunner/jobs/claim', ['type' => 'client_advice'])->assertUnauthorized();
    }

    public function test_claim_returns_no_content_when_queue_is_empty(): void
    {
        $this->worker('POST', '/api/pyrunner/jobs/claim', ['type' => 'client_advice'])->assertNoContent();
    }

    public function test_submitting_creates_request_and_job_and_triggers_pyrunner(): void
    {
        $problem = $this->submitProblem();

        $this->assertSame(ProblemRequest::STATUS_PROCESSING, $problem->status);
        $job = $problem->automationJob;
        $this->assertSame('pending', $job->status);
        $this->assertSame('client_advice', $job->type);
        $this->assertSame('Care & support', $job->payload['organisation']['type']);
        $this->assertSame('Urgent', $job->payload['problem']['urgency']);

        Http::assertSent(fn ($request) => $request->url() === 'https://pyrunner.test/webhook/abc/');
    }

    public function test_form_validates_required_answers(): void
    {
        Livewire::actingAs($this->business->owner)
            ->test(SubmitProblem::class)
            ->set('title', '')
            ->set('description', 'too short')
            ->call('submit')
            ->assertHasErrors(['title' => 'required', 'description' => 'min']);

        $this->assertSame(0, ProblemRequest::count());
    }

    public function test_full_flow_from_submission_to_approved_report(): void
    {
        $problem = $this->submitProblem();

        // PyRunner claims the job
        $claim = $this->worker('POST', '/api/pyrunner/jobs/claim', ['type' => 'client_advice'])->assertOk();
        $this->assertSame($problem->automationJob->uuid, $claim->json('uuid'));
        $this->assertSame('Rotas take a whole day', $claim->json('payload.problem.title'));

        // ...and sends back a draft
        $this->worker('POST', "/api/pyrunner/jobs/{$claim->json('uuid')}/complete", [
            'result' => ['draft' => "## Summary\nUse a rota tool."],
        ])->assertOk();

        $problem->refresh();
        $this->assertSame(ProblemRequest::STATUS_PENDING_REVIEW, $problem->status);
        $this->assertStringContainsString('Use a rota tool.', $problem->draft_report);
        Mail::assertSent(DraftReadyMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));

        // Client can't see the draft yet
        $this->actingAs($this->business->owner)->get(route('problems.show', $problem))
            ->assertOk()
            ->assertSee("We're preparing your report", false)
            ->assertDontSee('Use a rota tool.');

        // Owner edits and approves in /ops
        $admin = User::factory()->admin()->create();
        Filament::setCurrentPanel(Filament::getPanel('ops'));
        Livewire::actingAs($admin)
            ->test(EditProblemRequest::class, ['record' => $problem->getRouteKey()])
            ->fillForm(['draft_report' => "## Summary\nUse a rota tool. Edited by owner."])
            ->callAction('approve')
            ->assertHasNoActionErrors();

        $problem->refresh();
        $this->assertSame(ProblemRequest::STATUS_APPROVED, $problem->status);
        $this->assertNotNull($problem->approved_at);
        Mail::assertSent(ReportReadyMail::class, fn ($mail) => $mail->hasTo($this->business->owner->email));

        // Client now sees the edited report, rendered as HTML
        $this->actingAs($this->business->owner)->get(route('problems.show', $problem))
            ->assertOk()
            ->assertSee('<h2>Summary</h2>', false)
            ->assertSee('Edited by owner.');

        $this->actingAs($this->business->owner)->get(route('dashboard'))
            ->assertSee('Rotas take a whole day')
            ->assertSee('Report ready');
    }

    public function test_failed_job_marks_request_failed_and_emails_owner(): void
    {
        $problem = $this->submitProblem();
        $uuid = $problem->automationJob->uuid;

        $this->worker('POST', '/api/pyrunner/jobs/claim', ['type' => 'client_advice'])->assertOk();
        $this->worker('POST', "/api/pyrunner/jobs/{$uuid}/fail", ['error' => 'HTTPError: 401 bad key'])->assertOk();

        $problem->refresh();
        $this->assertSame(ProblemRequest::STATUS_FAILED, $problem->status);
        $this->assertSame('HTTPError: 401 bad key', $problem->error_message);
        Mail::assertSent(AdviceRequestFailedMail::class);

        // Owner retries: a fresh pending job is queued
        $admin = User::factory()->admin()->create();
        Filament::setCurrentPanel(Filament::getPanel('ops'));
        Livewire::actingAs($admin)
            ->test(EditProblemRequest::class, ['record' => $problem->getRouteKey()])
            ->callAction('retry');

        $problem->refresh();
        $this->assertSame(ProblemRequest::STATUS_PROCESSING, $problem->status);
        $this->assertNotSame($uuid, $problem->automationJob->uuid);
        $this->assertSame('pending', $problem->automationJob->status);
    }

    public function test_empty_draft_counts_as_failure(): void
    {
        $problem = $this->submitProblem();
        $uuid = $problem->automationJob->uuid;

        $this->worker('POST', "/api/pyrunner/jobs/{$uuid}/complete", ['result' => ['draft' => '   ']])->assertOk();

        $this->assertSame(ProblemRequest::STATUS_FAILED, $problem->fresh()->status);
    }

    public function test_stuck_jobs_are_failed_by_the_scheduled_command(): void
    {
        $problem = $this->submitProblem();
        $problem->automationJob->forceFill(['created_at' => now()->subHour()])->save();

        $this->artisan('portal:fail-stuck-jobs')->assertSuccessful();

        $this->assertSame(ProblemRequest::STATUS_FAILED, $problem->fresh()->status);
        $this->assertSame('failed', $problem->automationJob->fresh()->status);
        Mail::assertSent(AdviceRequestFailedMail::class);
    }

    public function test_late_draft_after_stuck_sweep_is_still_kept(): void
    {
        $problem = $this->submitProblem();
        $uuid = $problem->automationJob->uuid;
        $problem->automationJob->forceFill(['created_at' => now()->subHour()])->save();
        $this->artisan('portal:fail-stuck-jobs');

        $this->worker('POST', "/api/pyrunner/jobs/{$uuid}/complete", ['result' => ['draft' => 'Late but good']])->assertOk();

        $this->assertSame(ProblemRequest::STATUS_PENDING_REVIEW, $problem->fresh()->status);
    }

    public function test_clients_cannot_see_other_businesses_requests(): void
    {
        $problem = $this->submitProblem();
        $other = Business::factory()->active()->create();

        $this->actingAs($other->owner)->get(route('problems.show', $problem))->assertNotFound();
    }

    public function test_only_admins_can_open_the_review_panel(): void
    {
        $this->actingAs($this->business->owner)->get('/ops')->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get('/ops/problem-requests')->assertOk();
    }

    public function test_pyrunner_webhook_failure_does_not_block_submission(): void
    {
        Http::fake(['pyrunner.test/*' => fn () => throw new \Illuminate\Http\Client\ConnectionException('down')]);

        $problem = $this->submitProblem();

        $this->assertSame('pending', $problem->automationJob->status);
    }
}
