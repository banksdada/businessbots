<?php

namespace Tests\Feature;

use App\Mail\LoginLinkMail;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_displayed(): void
    {
        $this->get('/login')->assertOk()->assertSee('Welcome back');
    }

    public function test_register_page_is_displayed(): void
    {
        $this->get('/register')->assertOk()->assertSee('Create your account');
    }

    public function test_login_emails_a_magic_link(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'jo@example.com']);

        $this->post('/login', ['email' => 'jo@example.com'])
            ->assertRedirect(route('login.sent', ['email' => 'jo@example.com']));

        Mail::assertSent(LoginLinkMail::class, fn ($mail) => $mail->hasTo('jo@example.com'));
        $this->assertGuest();
        $this->assertSame(1, LoginToken::where('user_id', $user->id)->count());
    }

    public function test_login_requires_a_valid_email(): void
    {
        $this->post('/login', ['email' => 'not-an-email'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_register_creates_the_user_with_their_name(): void
    {
        Mail::fake();

        $this->post('/register', ['name' => 'Ada Lovelace', 'email' => 'Ada@Example.com'])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'ada@example.com', 'name' => 'Ada Lovelace']);
        Mail::assertSent(LoginLinkMail::class);
    }

    public function test_magic_link_logs_in_once(): void
    {
        $user = User::factory()->create();
        $token = LoginToken::issueFor($user);

        $this->get(route('login.verify', $token->token))->assertRedirect(route('onboarding'));
        $this->assertAuthenticatedAs($user);

        auth()->logout();

        $this->get(route('login.verify', $token->token))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }
}
