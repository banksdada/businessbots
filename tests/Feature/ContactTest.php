<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['portal.owner_email' => 'owner@example.com']);
        Mail::fake();
    }

    public function test_pricing_email_us_button_opens_the_contact_page(): void
    {
        $this->get('/pricing')->assertOk()->assertSee(route('contact'), false)->assertDontSee('mailto:', false);
        $this->get('/contact')->assertOk()->assertSee('Talk to us');
    }

    public function test_message_is_emailed_to_the_owner_with_reply_to_the_visitor(): void
    {
        $this->post('/contact', [
            'name' => 'Grace',
            'email' => 'grace@church.test',
            'organisation' => 'Grace Church',
            'message' => 'How much would it cost to fix our rota problem?',
        ])->assertRedirect('/contact')->assertSessionHas('status');

        Mail::assertSent(ContactMessageMail::class, fn ($mail) => $mail->hasTo('owner@example.com')
            && $mail->hasReplyTo('grace@church.test')
            && $mail->data['organisation'] === 'Grace Church');
    }

    public function test_contact_form_checks_answers(): void
    {
        $this->post('/contact', ['name' => '', 'email' => 'not-an-email', 'message' => 'hi'])
            ->assertSessionHasErrors(['name', 'email', 'message']);

        Mail::assertNothingSent();
    }

    public function test_spam_trap_sends_nothing(): void
    {
        $this->post('/contact', [
            'name' => 'Bot', 'email' => 'bot@spam.test', 'message' => 'Buy cheap things now please',
            'website' => 'http://spam.test',
        ])->assertRedirect('/contact');

        Mail::assertNothingSent();
    }

    public function test_report_page_help_button_prefills_the_message(): void
    {
        $this->get('/contact?about=Rotas take a whole day')
            ->assertOk()
            ->assertSee('help putting the fix in place for: Rotas take a whole day', false);
    }
}
