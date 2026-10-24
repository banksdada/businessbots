<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Voice notes — record on a phone, get text back in the form.
 *
 * The browser records audio as a WebM blob and posts it here; we run it
 * through OpenAI Whisper (or any OpenAI-compatible /audio/transcriptions
 * endpoint) and return the plain text, which the client drops into the
 * problem form. Nothing is stored — the audio is transcribed and discarded.
 *
 * Authenticated only: this is the client advice portal, not a public API.
 */
class VoiceNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * POST /api/voice-notes/transcribe
     *
     * Accepts a single "audio" file (WebM, from MediaRecorder) and returns
     * JSON: { "text": "..." }. Errors are 4xx with a readable message.
     */
    public function transcribe(Request $request)
    {
        $request->validate([
            'audio' => ['required', 'file', 'mimes:webm,mp4,m4a,ogg,wav', 'max:1048576'],
        ], [
            'audio.required' => 'No audio was recorded. Try again.',
            'audio.max'      => 'That recording is too long. Keep it under a minute.',
        ]);

        /** @var UploadedFile $audio */
        $audio = $request->file('audio');

        // Cap it: a minute of voice is plenty for a problem description.
        if ($audio->getSize() > 1048576) {
            return response()->json(['message' => 'That recording is too long. Keep it under a minute.'], 413);
        }

        $provider = config('voice.provider', 'openai');

        try {
            $text = match ($provider) {
                'openai' => $this->transcribeOpenAI($audio),
                default  => $this->transcribeOpenAI($audio),
            };
        } catch (\Throwable $e) {
            Log::error('Voice note transcription failed', [
                'message' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'We couldn\'t transcribe that one. Please try again, or type it instead.',
            ], 502);
        }

        // Whisper can return empty for silence — say so rather than writing
        // a blank into the form.
        $text = trim((string) $text);

        if ($text === '') {
            return response()->json([
                'message' => 'We didn\'t catch anything. Try speaking a bit closer to the mic.',
            ], 422);
        }

        return response()->json(['text' => $text]);
    }

    /**
     * OpenAI-compatible /audio/transcriptions. Works with OpenAI directly,
     * or any gateway that exposes the same endpoint (e.g. a self-hosted
     * whisper model behind an OpenAI-compatible base URL).
     */
    private function transcribeOpenAI(UploadedFile $audio): string
    {
        $baseUrl = rtrim(config('ai.base_url', 'https://api.openai.com/v1'), '/');
        // OpenAI's own endpoint lives at /audio/transcriptions; a generic
        // gateway may not. Only append it when talking to api.openai.com.
        $url = str_contains($baseUrl, 'api.openai.com')
            ? $baseUrl . '/audio/transcriptions'
            : $baseUrl . '/audio/transcriptions';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('ai.api_key'),
        ])
            ->timeout(60)
            ->attach('file', $audio->getContents(), $audio->getClientOriginalName() ?: 'voice.webm')
            ->post($url, [
                'model' => config('voice.model', 'whisper-1'),
                'response_format' => 'text',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Whisper returned ' . $response->status() . ': ' . $response->body());
        }

        return $response->body();
    }
}