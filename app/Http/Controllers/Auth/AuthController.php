<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginLinkMail;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $email = strtolower($request->email);

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $this->nameFromEmail($email),
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