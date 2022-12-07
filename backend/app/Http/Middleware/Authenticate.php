<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Data\Models\Token;
use App\Data\Models\User;
use App\Domains\Auth\Jobs\GetUserLastSeenJob;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authentication($request, $guards);

        return $next($request);
    }

    /**
     * @phpstan-ignore-next-line
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authentication($request, ...$guards)
    {
        $this->authenticate($request, $guards);

        /** @var User $user */
        $user = $request->user();
        $token = Auth::token();

        if ($user->is_banned || !$user->activated_at) {
            $this->unauthenticated($request, $guards);
        }

        if (isset($token->act) && $token->act === Token::ACTION_LOGIN_AS) {
            return;
        }

        Cache::forever(sprintf(GetUserLastSeenJob::CACHE_KEY, $user->id), time());
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        return null;
    }
}
