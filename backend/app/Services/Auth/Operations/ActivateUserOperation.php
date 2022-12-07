<?php

declare(strict_types=1);

namespace App\Services\Auth\Operations;

use App\Data\Models\User;
use App\Domains\Auth\Jobs\SendMailNotificationToUserJob;
use App\Domains\Auth\Notifications\Welcome;
use App\Domains\Common\Jobs\GenerateUserStatisticJob;
use App\Domains\Common\Jobs\UpdateUserStatisticReferralJob;
use App\Domains\Referral\Jobs\GenerateReferralDataJob;
use Lucid\Units\QueueableOperation;

class ActivateUserOperation extends QueueableOperation
{
    public function __construct(
        protected User $user,
        protected string $locale
    ) {
    }

    public function handle(): void
    {
        $this->run(SendMailNotificationToUserJob::class, [
            'user' => $this->user,
            'notification' => new Welcome($this->locale),
        ]);

        $this->run(UpdateUserStatisticReferralJob::class, [
            'user' => $this->user,
        ]);

        $this->run(GenerateUserStatisticJob::class, [
            'user' => $this->user,
        ]);

        $this->run(GenerateReferralDataJob::class, [
            'user' => $this->user,
        ]);
    }
}
