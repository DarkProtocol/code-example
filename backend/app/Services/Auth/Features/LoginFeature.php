<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Http\Exceptions\ApiException;
use App\Data\Models\Token;
use App\Data\Models\User;
use App\Domains\Auth\Jobs\GetUserByEmailJob;
use App\Domains\Auth\Requests\LoginRequest;
use App\Services\Auth\Operations\IssueTokensOperation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Lucid\Units\Feature;

class LoginFeature extends Feature
{
    /**
     * @throws ValidationException
     */
    public function handle(LoginRequest $request): mixed
    {
        /** @var User|null $user */
        $user = $this->run(GetUserByEmailJob::class, ['email' => $request->input('email')]);

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            throw new ApiException(['email' => __('auth.password')]);
        }

        if ($user->role_id === User::ROLE_BOT) {
            throw new ApiException(['email' => __('auth.password')]);
        }

        if (!$user->activated_at) {
            throw new ApiException(['email' => __('auth.inactive')]);
        }

        if ($user->is_banned) {
            throw new ApiException(['email' => __('auth.banned')]);
        }

        return $this->run(IssueTokensOperation::class, [
            'user' => $user,
            'action' => Token::ACTION_LOGIN,
            'createdBy' => $user,
        ]);
    }
}
