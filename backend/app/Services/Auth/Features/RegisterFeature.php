<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Http\Exceptions\InternalError;
use App\Data\Models\User;
use App\Domains\Auth\Jobs\CreateUserJob;
use App\Domains\Auth\Requests\RegisterRequest;
use App\Domains\Http\Jobs\DetectRequestCountryJob;
use App\Services\Auth\Operations\SendEmailConfirmationMailOperation;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Lucid\Units\Feature;
use Exception;
use ReflectionException;

class RegisterFeature extends Feature
{
    /**
     * @throws ReflectionException
     * @throws InternalError
     */
    public function handle(RegisterRequest $request, Logger $logger): mixed
    {
        $requestCountry = $this->run(DetectRequestCountryJob::class, [
            'detectTor' => true,
        ]);

        try {
            DB::beginTransaction();
            /** @var User $user */
            $user = $this->run(CreateUserJob::class, [
                'email' => $request->input('email'),
                'nickname' => $request->input('nickname'),
                'password' => $request->input('password'),
                'ip' => $request->ip(),
                'country' => $requestCountry,
                'ua' => $request->userAgent(),
                'referrer' => $request->input('referrer'),
            ]);

            $this->run(GenerateUserSettingsJob::class, [
                'user' => $user,
            ]);

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            $logger->error('Register error: ' . $e->getMessage());
            throw new InternalError();
        }

        $this->runInQueue(SendEmailConfirmationMailOperation::class, [
            'user' => $user,
            'locale' => App::getLocale(),
        ]);

        return response()->json(null, 201);
    }
}
