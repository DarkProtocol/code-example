<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Lucid\Units\Job;

class SendMailNotificationToUserJob extends Job
{
    protected User $user;
    protected Notification $notification;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param Notification $notification
     */
    public function __construct(
        User $user,
        Notification $notification
    ) {
        $this->user = $user;
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if (!App::environment('local') || env('EMAIL_DEBUG')) {
            $this->user->notify($this->notification);
        }
    }
}
