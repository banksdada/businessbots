<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'portal:make-admin {email}';

    protected $description = 'Let a user review every client request in the /ops panel';

    public function handle(): int
    {
        $email = strtolower($this->argument('email'));

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => strstr($email, '@', true) ?: 'Admin', 'password' => bin2hex(random_bytes(16))],
        );

        $user->forceFill(['is_admin' => true])->save();

        $this->info("{$email} is now an admin. Sign in with a magic link, then open /ops.");

        return self::SUCCESS;
    }
}
