<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class OptionalAuthentication extends Authenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authentication($request, $guards);
        } catch (AuthenticationException $e) {
            // don't do anything
        }

        return $next($request);
    }
}
