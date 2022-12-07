<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Http\Exceptions\ApiException;
use App\Common\Http\Exceptions\InternalError;
use App\Data\Models\Token;
use App\Data\Models\User;
use App\Domains\Auth\Jobs\ActivateUserByEmailConfirmationCodeJob;
use App\Domains\Auth\Requests\UserActivateRequest;
use App\Services\Auth\Operations\ActivateUserOperation;
use App\Services\Auth\Operations\IssueTokensOperation;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\App;
use Lucid\Units\Feature;
use Exception;

class UserActivateFeature extends Feature
{
    public function handle(UserActivateRequest $request, Logger $logger): mixed
    {
        try {
            /** @var User|null $user */
            $user = $this->run(ActivateUserByEmailConfirmationCodeJob::class, [
                'code' => $request->input('code'),
            ]);

            if ($user) {
                $this->runInQueue(ActivateUserOperation::class, [
                    'user' => $user,
                    'locale' => App::getLocale(),
                ]);

                return $this->run(IssueTokensOperation::class, [
                    'user' => $user,
                    'action' => Token::ACTION_USER_ACTIVATE,
                    'createdBy' => $user,
                ]);
            }
        } catch (Exception $e) {
            $logger->error('Activate user error: ' . $e->getMessage());
            throw new InternalError();
        }

        throw new ApiException(['email' => __('auth.user-not-found')]);
    }
}
