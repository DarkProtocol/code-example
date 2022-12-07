<?php

declare(strict_types=1);

namespace App\Services\Auth\Console;

use App\Data\Models\User;
use Illuminate\Console\Command;
use Illuminate\Log\Logger;
use Throwable;

class UnbanUserCommand extends Command
{
    protected $signature = 'auth:unban-user {email}';

    protected $description = 'Unban user';

    public function handle(Logger $logger): void
    {
        if (!$this->argument('email')) {
            $this->error('Email is required');
            return;
        }

        /** @var User|null $user */
        $user = User::where('email', mb_strtolower($this->argument('email')))->first();

        if (!$user) {
            $this->error('User not found');
            return;
        }

        if (!$user->is_banned) {
            $this->error('User is not in ban');
            return;
        }

        $confirm = $this->confirm('Unban user "' . $user->nickname . '"?', true);

        if (!$confirm) {
            $this->error('Canceled!');
            return;
        }

        try {
            $user->is_banned = false;
            $user->saveOrFail();
            $this->info('User was unbanned!');
        } catch (Throwable $e) {
            $logger->error('Error in user unban: ' . $e->getMessage());
            $this->error('Error!');
        }
    }
}
