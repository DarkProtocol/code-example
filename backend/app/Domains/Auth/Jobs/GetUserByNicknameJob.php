<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\User;
use Lucid\Units\Job;

class GetUserByNicknameJob extends Job
{
    public function __construct(protected string $nickname)
    {
    }

    public function handle(): ?User
    {
        return User::where([
            'nickname' => $this->nickname,
            'is_banned' => false,
        ])->first();
    }
}
