<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Lucid\Units\Job;

class RevokeTokensBySessionIdJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @param string $sessionId
     */
    public function __construct(
        protected string $sessionId
    ) {
    }

    /**
     * Execute the job.
     *
     * @param Request $request
     * @return void
     */
    public function handle(Request $request): void
    {
        $revocationDate = Carbon::now();

        $tokens = Token::query()
            ->select('id', 'expire_at')
            ->where('session_id', $this->sessionId)
            ->where('expire_at', '>', $revocationDate)
            ->get();

        if ($tokens->isEmpty()) {
            $this->forgetCookies($request);
            return;
        }

        foreach ($tokens as $token) {
            $key = sprintf(Token::REVOKED_CACHE_KEY, $token->id);

            Cache::put($key, $revocationDate, $token->expire_at->addMinutes(5));
        }

        Token::where('session_id', $this->sessionId)->update([
            'revoked_at' => $revocationDate,
        ]);

        $this->forgetCookies($request);
    }

    /**
     * Forget cookies
     *
     * @param Request $request
     */
    protected function forgetCookies(Request $request): void
    {
        $domain = implode('.', array_slice(explode('.', $request->getHttpHost()), -2));

        Cookie::queue(Cookie::forget(
            Token::COOKIE_AUTHORIZED,
            '/',
            $domain
        ));

        Cookie::queue(Cookie::forget(
            Token::COOKIE_ACCESS,
            '/',
            $domain
        ));

        Cookie::queue(Cookie::forget(
            Token::COOKIE_REFRESH,
            route('auth.token.refresh', [], false),
            $request->getHttpHost()
        ));
    }
}
