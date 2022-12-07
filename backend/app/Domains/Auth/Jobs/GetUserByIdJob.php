<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\User;
use Lucid\Units\Job;

class GetUserByIdJob extends Job
{
    public function __construct(protected string $id)
    {
    }

    public function handle(): ?User
    {
        return User::where([
            'id' => $this->id,
            'is_banned' => false,
        ])->first();
    }
}
