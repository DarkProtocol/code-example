<?php

declare(strict_types=1);

namespace App\Common\Http\Middleware;

use App\Data\Models\User;
use Closure;
use Illuminate\Http\Request;

class SentryIdentity
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if ($user instanceof User && app()->bound('sentry')) {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($user, $request): void {
                $scope->setUser([
                    'id'    => $user->id,
                    'email' => $user->email,
                    'username'  => $user->nickname,
                    'ip_address' => $request->ip(),
                ]);
            });
        }

        return $next($request);
    }
}
