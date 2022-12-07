<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Http\Exceptions\ApiException;
use App\Common\Http\Exceptions\InternalError;
use App\Data\Models\User;
use App\Domains\Auth\Requests\ChangePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Hash;
use Lucid\Units\Feature;
use Throwable;

class ChangePasswordFeature extends Feature
{
    /**
     * @param ChangePasswordRequest $request
     * @param Logger $logger
     * @return JsonResponse
     * @throws ApiException
     * @throws InternalError
     */
    public function handle(ChangePasswordRequest $request, Logger $logger): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (!Hash::check($request->input('oldPassword'), $user->password)) {
            throw new ApiException(['oldPassword' => __('auth.wrong-old-password')]);
        }

        $user->password = Hash::make($request->input('newPassword'));

        try {
            $user->saveOrFail();
        } catch (Throwable $e) {
            $logger->error('Error in change user password: ' . $e->getMessage());
            throw new InternalError();
        }

        return response()->json(null, 204);
    }
}
