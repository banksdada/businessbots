<?php

namespace App\Services\Advice;

use App\Livewire\Onboarding\VerticalStep;
use App\Mail\AdviceRequestFailedMail;
use App\Mail\DraftReadyMail;
use App\Mail\ReportReadyMail;
use App\Models\AutomationJob;
use App\Models\Business;
use App\Models\ProblemRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * The client advice flow, end to end:
 *
 *   submit()        client's form  → ProblemRequest + pending AutomationJob → kick PyRunner
 *   (PyRunner claims the job, asks the AI, then calls complete or fail)
 *   jobCompleted()  draft saved    → pending_review, owner emailed
 *   jobFailed()     error saved    → failed, owner emailed
 *   approve()       owner approves → final report, client emailed
 */
class AdviceRequestService
{
    public function submit(User $user, Business $business, array $answers): ProblemRequest
    {
        $request = DB::transaction(function () use ($user, $business, $answers) {
            $request = ProblemRequest::create([
                'business_id' => $business->id,
                'user_id' => $user->id,
                'title' => $answers['title'],
                'description' => $answers['description'],
                'already_tried' => $answers['already_tried'] ?? null,
                'current_tools' => $answers['current_tools'] ?? null,
                'staff_count' => $answers['staff_count'] ?? null,
                'urgency' => $answers['urgency'],
                'help_wanted' => $answers['help_wanted'],
                'status' => ProblemRequest::STATUS_PROCESSING,
            ]);

            $this->queueJob($request);

            return $request;
        });

        $this->triggerPyRunner();

        return $request;
    }

    /** Owner-initiated retry of a failed request: fresh job, same answers. */
    public function retry(ProblemRequest $request): void
    {
        DB::transaction(function () use ($request) {
            $request->update([
                'status' => ProblemRequest::STATUS_PROCESSING,
                'error_message' => null,
            ]);

            $this->queueJob($request);
        });

        $this->triggerPyRunner();
    }

    public function jobCompleted(AutomationJob $job): void
    {
        $request = $job->problemRequest;

        // A late draft for a request the stuck-job sweep already failed is
        // still worth keeping, as long as it belongs to the latest job.
        if (! $request || ! in_array($request->status, [ProblemRequest::STATUS_PROCESSING, ProblemRequest::STATUS_FAILED], true)) {
            return;
        }

        $draft = trim((string) data_get($job->result, 'draft', ''));

        if ($draft === '') {
            $this->markFailed($request, 'The AI script finished but returned an empty draft.');
            return;
        }

        $request->update([
            'status' => ProblemRequest::STATUS_PENDING_REVIEW,
            'draft_report' => $draft,
            'error_message' => null,
        ]);

        $this->notifyOwner(new DraftReadyMail($request));
    }

    public function jobFailed(AutomationJob $job): void
    {
        $request = $job->problemRequest;

        if (! $request || $request->status !== ProblemRequest::STATUS_PROCESSING) {
            return;
        }

        $this->markFailed($request, $job->error_message ?? 'Unknown error');
    }

    public function approve(ProblemRequest $request, string $finalReport): void
    {
        $request->update([
            'status' => ProblemRequest::STATUS_APPROVED,
            'draft_report' => $finalReport,
            'final_report' => $finalReport,
            'approved_at' => now(),
        ]);

        try {
            Mail::to($request->user->email)->send(new ReportReadyMail($request));
        } catch (\Throwable $e) {
            // The report is still on the client's dashboard; don't undo the approval.
            Log::error('[Advice] client report email failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Jobs stuck in "processing" past the limit are failed, so a crashed or
     * timed-out script never leaves a client waiting with no one noticing.
     */
    public function failStuckJobs(): int
    {
        $cutoff = now()->subMinutes(config('portal.stuck_after_minutes'));

        $stuck = AutomationJob::query()
            ->where('type', config('portal.job_type'))
            ->whereIn('status', ['pending', 'processing'])
            ->where('created_at', '<', $cutoff)
            ->get();

        foreach ($stuck as $job) {
            $job->update([
                'status' => 'failed',
                'error_message' => 'No result from PyRunner after ' . config('portal.stuck_after_minutes') . ' minutes.',
                'failed_at' => now(),
            ]);

            $this->jobFailed($job);
        }

        return $stuck->count();
    }

    private function queueJob(ProblemRequest $request): AutomationJob
    {
        $business = $request->business;

        $job = AutomationJob::create([
            'uuid' => (string) Str::uuid(),
            'business_id' => $request->business_id,
            'user_id' => $request->user_id,
            'type' => config('portal.job_type'),
            'status' => 'pending',
            'queued_at' => now(),
            'payload' => [
                'problem_request_id' => $request->id,
                'organisation' => [
                    'name' => $business->name,
                    'type' => VerticalStep::labelFor($business->verticalType()),
                    'location' => $business->location,
                    'description' => $business->description,
                    'staff_count' => $request->staff_count,
                ],
                'problem' => [
                    'title' => $request->title,
                    'description' => $request->description,
                    'already_tried' => $request->already_tried,
                    'current_tools' => $request->current_tools,
                    'urgency' => ProblemRequest::URGENCIES[$request->urgency] ?? $request->urgency,
                    'help_wanted' => ProblemRequest::HELP_OPTIONS[$request->help_wanted] ?? $request->help_wanted,
                ],
            ],
        ]);

        $request->update(['automation_job_id' => $job->id]);

        return $job;
    }

    /**
     * Start the PyRunner script now instead of waiting for its schedule. Best
     * effort: if this call fails, the 5-minute schedule still picks the job up.
     */
    private function triggerPyRunner(): void
    {
        $url = config('services.pyrunner.advice_webhook');

        if (! $url) {
            return;
        }

        try {
            Http::timeout(5)->post($url, ['source' => 'businessbots']);
        } catch (\Throwable $e) {
            Log::warning('[Advice] PyRunner webhook call failed; schedule will pick the job up', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function markFailed(ProblemRequest $request, string $error): void
    {
        $request->update([
            'status' => ProblemRequest::STATUS_FAILED,
            'error_message' => Str::limit($error, 2000),
        ]);

        $this->notifyOwner(new AdviceRequestFailedMail($request));
    }

    private function notifyOwner($mailable): void
    {
        $to = config('portal.owner_email');

        if (! $to) {
            Log::warning('[Advice] PORTAL_OWNER_EMAIL is not set; owner not notified');
            return;
        }

        try {
            Mail::to($to)->send($mailable);
        } catch (\Throwable $e) {
            // A mail outage must not undo the saved draft or status change.
            Log::error('[Advice] owner email failed', ['error' => $e->getMessage()]);
        }
    }
}
