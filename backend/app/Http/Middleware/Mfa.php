<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Common\Http\Exceptions\ApiException;
use App\Common\Http\Exceptions\InternalError;
use App\Data\Models\MfaAction;
use App\Data\Models\User;
use App\Domains\Http\Jobs\DetectRequestCountryJob;
use App\Domains\Mfa\Jobs\CreateActionJob;
use App\Domains\Mfa\Jobs\GetNonExpiredActionByUserAndIdJob;
use App\Domains\Mfa\Jobs\GetNonExpiredActionByUserAndTypeJob;
use App\Domains\Mfa\Jobs\ValidateMfaActionInputJob;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Lucid\Bus\UnitDispatcher;
use Exception;

class Mfa
{
    use UnitDispatcher;

    public function handle(Request $request, Closure $next, mixed $type): mixed
    {
        /** @var User $user */
        $user = $request->user();

        if (Cache::has(MfaAction::getBannedUserKey($user->id))) {
            throw new ApiException(null, __('auth.action-many-attempts'));
        }

        if ($user->activeMfaMethods->count() === 0) {
            return $next($request);
        }

        try {
            $logger = resolve('log');
        } catch (Exception $e) {
            return null;
        }

        if (!$request->has('actionId')) {
            /** @var MfaAction|null $action */
            $action = $this->run(GetNonExpiredActionByUserAndTypeJob::class, [
                'user' => $user,
                'type' => $type,
            ]);

            if (!$action || $action->status !== MfaAction::STATUS_NEW) {
                $requestCountry = $this->run(DetectRequestCountryJob::class, [
                    'detectTor' => true,
                ]);

                try {
                    $action = $this->run(CreateActionJob::class, [
                        'user' => $user,
                        'type' => $type,
                        'requestCountry' => $requestCountry,
                    ]);
                } catch (Exception $e) {
                    $logger->error('Error on create action: ' . $e->getMessage());
                    throw new InternalError();
                }
            }

            return response()->json([
                'actionId' => $action->id,
                'expiredAt' => $action->expired_at,
            ], 403);
        }

        if (!$this->run(ValidateMfaActionInputJob::class, ['input' => $request->input()])) {
            return response()->json(null, 400);
        }

        /** @var MfaAction|null $action */
        $action = $this->run(GetNonExpiredActionByUserAndIdJob::class, [
            'user' => $user,
            'id' => $request->input('actionId'),
        ]);

        if (!$action || $action->type !== $type || $action->status !== MfaAction::STATUS_PASSED) {
            return response()->json(null, 400);
        }

        try {
            $action->status = MfaAction::STATUS_COMPLETED;
            $action->saveOrFail();
        } catch (Exception $e) {
            $logger->error('Error on complete action: ' . $e->getMessage());
            throw new InternalError();
        }

        return $next($request);
    }
}
