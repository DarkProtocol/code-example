<?php

declare(strict_types=1);

namespace App\Domains\Auth\Jobs;

use App\Data\Models\Token;
use App\Data\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Lucid\Units\Job;
use Throwable;

class IssueRefreshTokenJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $sessionId
     * @param string|null $requestCountry
     */
    public function __construct(
        protected User $user,
        protected string $sessionId,
        protected string $action,
        protected User $createdBy,
        protected ?string $requestCountry = null
    ) {
    }

    /**
     * Execute the job.
     *
     * @param Request $request
     * @return string
     * @throws Throwable
     */
    public function handle(Request $request): string
    {
        $expire = Carbon::now()->addSeconds(Token::LIFETIME_REFRESH);

        $token = new Token();
        $token->type = Token::TYPE_REFRESH;
        $token->user_id = $this->user->id;
        $token->session_id = $this->sessionId;
        $token->create_ip = $request->ip();
        $token->create_country = $this->requestCountry;
        $token->action = $this->action;
        $token->created_by = $this->createdBy->id;
        $token->create_ua = $request->userAgent();
        $token->expire_at = $expire;
        $token->saveOrFail();

        $payload = [
            'jti' => $token->id,
            'iss' => config('app.name'),
            'iat' => Carbon::now()->unix(),
            'exp' => $expire->unix(),
            'type' => Token::TYPE_REFRESH,
            'sid' => $this->sessionId,
            'sub' => $this->user->id,
        ];

        $key = base64_decode(env('JWT_PRIVATE_KEY'));

        $jwt = JWT::encode($payload, $key, env('JWT_ALGO'));

        $expireMinutes = Carbon::now()->diffInMinutes($expire, true);

        Cookie::queue(Cookie::make(
            Token::COOKIE_REFRESH,
            $jwt,
            $expireMinutes,
            route('auth.token.refresh', [], false),
            $request->getHttpHost(),
            !App::environment('local'),
            true,
            false,
            'Strict'
        ));

        return $jwt;
    }
}
