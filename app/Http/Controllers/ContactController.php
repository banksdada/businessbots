<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * "Email us" without needing a mail app: the visitor fills in a short form and
 * we email it to the owner (PORTAL_OWNER_EMAIL), with Reply-To set to them.
 */
class ContactController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('marketing.contact', [
            'name' => $user?->name,
            'email' => $user?->email,
            // e.g. /contact?about=Rotas+take+a+whole+day from a report page
            'about' => str($request->query('about', ''))->limit(150, '')->toString(),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        // Hidden field only bots fill in. Pretend it worked so they move on.
        if ($request->filled('website')) {
            return redirect()->route('contact')->with('status', 'Thanks! Your message has been sent. We\'ll reply by email soon.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'organisation' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'name.required' => 'Please tell us your name.',
            'email.required' => 'Please give an email address so we can reply.',
            'email.email' => 'That email address doesn\'t look right.',
            'message.required' => 'Please write a short message.',
            'message.min' => 'Please add a little more detail (at least 10 characters).',
        ]);

        $to = config('portal.owner_email');

        if (! $to) {
            Log::warning('[Contact] PORTAL_OWNER_EMAIL is not set; contact message not sent', ['from' => $data['email']]);

            return back()->withInput()->withErrors(['message' => 'Sorry, we couldn\'t send your message just now. Please try again later.']);
        }

        try {
            Mail::to($to)->send(new ContactMessageMail($data));
        } catch (\Throwable $e) {
            Log::error('[Contact] Sending contact message failed: ' . $e->getMessage());

            return back()->withInput()->withErrors(['message' => 'Sorry, we couldn\'t send your message just now. Please try again in a few minutes.']);
        }

        return redirect()->route('contact')->with('status', 'Thanks! Your message has been sent. We\'ll reply by email soon.');
    }
}
