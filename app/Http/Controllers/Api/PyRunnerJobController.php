<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutomationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PyRunnerJobController extends Controller
{
    private function authorised(Request $request): bool
    {
        $expected = config('services.pyrunner.worker_token');

        if (! $expected) {
            return false;
        }

        $provided = $request->bearerToken();

        return is_string($provided)
            && hash_equals($expected, $provided);
    }

    public function claim(Request $request)
    {
        if (! $this->authorised($request)) {
            return response()->json([
                'message' => 'Unauthorised',
            ], 401);
        }

        $validated = $request->validate([
            'type' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $job = DB::transaction(function () use ($validated) {

            $job = AutomationJob::query()
                ->where('type', $validated['type'])
                ->where('status', 'pending')
                ->orderBy('created_at')
                ->lockForUpdate()
                ->first();

            if (! $job) {
                return null;
            }

            $job->update([
                'status' => 'processing',
                'started_at' => now(),
                'attempts' => $job->attempts + 1,
            ]);

            return $job->fresh();
        });

        if (! $job) {
            return response()->noContent();
        }

        return response()->json([
            'uuid' => $job->uuid,
            'type' => $job->type,
            'payload' => $job->payload,
        ]);
    }

    public function complete(
        Request $request,
        string $uuid
    ) {
        if (! $this->authorised($request)) {
            return response()->json([
                'message' => 'Unauthorised',
            ], 401);
        }

        $validated = $request->validate([
            'result' => [
                'required',
                'array',
            ],
        ]);

        $job = AutomationJob::where(
            'uuid',
            $uuid
        )->firstOrFail();

        if ($job->status === 'completed') {
            return response()->json([
                'message' => 'Already completed',
            ]);
        }

        $job->update([
            'status' => 'completed',
            'result' => $validated['result'],
            'completed_at' => now(),
            'error_message' => null,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function fail(
        Request $request,
        string $uuid
    ) {
        if (! $this->authorised($request)) {
            return response()->json([
                'message' => 'Unauthorised',
            ], 401);
        }

        $validated = $request->validate([
            'error' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $job = AutomationJob::where(
            'uuid',
            $uuid
        )->firstOrFail();

        $job->update([
            'status' => 'failed',
            'error_message' => $validated['error'],
            'failed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}