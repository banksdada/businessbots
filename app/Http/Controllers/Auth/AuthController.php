<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginLinkMail;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    /**
     * Send a one-time login link to the email. Works for both sign-in and
     * sign-up: if the email doesn't exist yet, we create the account first
     * (with a random, unusable password) and then send the link.
     */
    public function sendLoginLink(Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $email = strtolower($request->email);

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => trim((string) $request->input('name')) ?: $this->nameFromEmail($email),
                'email' => $email,
                'password' => bin2hex(random_bytes(16)),
            ]);
        }

        $token = LoginToken::issueFor($user);

        $url = route('login.verify', [
            'token' => $token->token,
        ]);

        Mail::to($user->email)->send(new LoginLinkMail($url));

        return redirect()->route('login.sent', ['email' => $user->email]);
    }

    /**
     * Log the user in from a valid, unused, unexpired token.
     */
    public function verify(Request $request, string $token)
    {
        $loginToken = LoginToken::where('token', $token)->first();

        if (! $loginToken || ! $loginToken->isValid()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'That login link is invalid or has expired. Request a new one.']);
        }

        $loginToken->consume();

        Auth::login($loginToken->user);
        $request->session()->regenerate();

        // --- AI‑OS integration: push lead to AI‑OS dashboard via webhook ---
        // Only fire on successful login; the token guarantees the email is verified.
        // We send a minimal payload (name and email) signed with the shared secret.
        // The login never fails because the webhook fails — we just log it.
        try {
            $user = $loginToken->user;
            $payload = [
                'name' => $user->name,
                'email' => $user->email,
            ];
            $secret = env('CRM_WEBHOOK_SECRET', '');
            $timestamp = now()->timestamp;
            $signature = hash_hmac(
                'sha256',
                $timestamp . '.' . json_encode($payload),
                $secret
            );
            $response = Http::withHeaders([
                'X-AIOS-Timestamp' => $timestamp,
                'X-AIOS-Signature' => $signature,
                'Content-Type' => 'application/json',
            ])->timeout(5)
              ->post(env('AIOS_WEBHOOK_URL', ''), $payload);

            if (! $response->successful()) {
                Log::warning('AI‑OS webhook failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'user_id' => $user->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('AI‑OS webhook exception', [
                'message' => $e->getMessage(),
                'user_id' => $loginToken->user->id,
            ]);
        }

        return redirect()->intended(route('onboarding'));
    }

    public function sent(Request $request)
    {
        return view('auth.sent', [
            'email' => $request->query('email'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function nameFromEmail(string $email): string
    {
        $name = strstr($email, '@', true) ?: 'there';

        return implode(' ', array_map('ucfirst', preg_split('/[._-]+/', $name) ?: []));
    }
}