<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Http\Exceptions\ApiException;
use App\Common\Http\Exceptions\InternalError;
use App\Data\Models\Token;
use App\Domains\Auth\Jobs\PasswordResetCompleteJob;
use App\Domains\Auth\Requests\PasswordResetCompleteRequest;
use App\Domains\Http\Jobs\DetectRequestCountryJob;
use App\Services\Auth\Operations\IssueTokensOperation;
use Illuminate\Log\Logger;
use Lucid\Units\Feature;
use Exception;

class PasswordResetCompleteFeature extends Feature
{
    /**
     * @param PasswordResetCompleteRequest $request
     * @param Logger $logger
     * @return mixed
     * @throws ApiException
     * @throws InternalError
     */
    public function handle(PasswordResetCompleteRequest $request, Logger $logger): mixed
    {
        $requestCountry = $this->run(DetectRequestCountryJob::class, [
            'detectTor' => true,
        ]);

        try {
            $user = $this->run(PasswordResetCompleteJob::class, [
                'token' => $request->input('token'),
                'ip' => $request->ip(),
                'country' => $requestCountry,
                'ua' => $request->userAgent(),
                'password' => $request->input('password'),
            ]);

            if ($user) {
                return $this->run(IssueTokensOperation::class, [
                    'user' => $user,
                    'action' => Token::ACTION_PASSWORD_RESET,
                    'createdBy' => $user,
                ]);
            }
        } catch (Exception $e) {
            $logger->error('Password reset complete error: ' . $e->getMessage());
            throw new InternalError();
        }

        throw new ApiException(null, __('auth.user-not-found'));
    }
}
