<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\File;
use App\Data\Models\User;
use App\Data\Models\UserSetting;
use Lucid\Units\Job;

class GenerateUserSettingsJob extends Job
{
    public function __construct(
        protected User $user
    ) {
    }

    public function handle(): UserSetting
    {
        $settings = new UserSetting();
        $settings->user_id = $this->user->id;
        $settings->game_sound = true;
        $settings->avatar_tariff = UserSetting::SINGLE_UPLOAD_AVATAR_TARIFF;

        /** @var File $avatar */
        $avatar = File::where('upload_type', File::SYSTEM_AVATAR_UPLOAD_TYPE)
            ->inRandomOrder()->first();

        $settings->avatar_file_id = $avatar->id;

        $settings->saveOrFail();

        return $settings;
    }
}
