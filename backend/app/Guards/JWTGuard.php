<?php

declare(strict_types=1);

namespace App\Guards;

use App\Data\Models\Token;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Throwable;
use UnexpectedValueException;

class JWTGuard implements Guard
{
    protected ?Authenticatable $user = null;
    protected ?object $token = null;

    public function __construct(
        protected UserProvider $userProvider
    ) {
        $jwt = Cookie::get(Token::COOKIE_ACCESS);

        if (!$jwt) {
            return;
        }

        $key = base64_decode(env('JWT_PUBLIC_KEY'));

        try {
            $this->token = JWT::decode($jwt, $key, [
                env('JWT_ALGO'),
            ]);

            $key = sprintf(Token::REVOKED_CACHE_KEY, $this->token->jti);

            if (Cache::store('auth')->has($key)) {
                // Token revoked
                $this->logout();
            }
        } catch (BeforeValidException | ExpiredException | SignatureInvalidException | UnexpectedValueException $e) {
            $this->logout();
        }
    }

    /**
     * @inheritDoc
     */
    public function check(): bool
    {
        if (!is_null($this->user)) {
            return true;
        }

        if (is_null($this->token)) {
            return false;
        }

        $this->user = $this->userProvider->retrieveById($this->token->sub);

        return !is_null($this->user);
    }

    /**
     * @inheritDoc
     */
    public function guest(): bool
    {
        return is_null($this->user);
    }

    /**
     * @inheritDoc
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * Get token
     *
     * @return object|null
     */
    public function token(): ?object
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function id()
    {
        return !is_null($this->user) ? $this->user->getAuthIdentifier() : null;
    }

    /**
     * @inheritDoc
     * @phpstan-ignore-next-line
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function hasUser(): bool
    {
        return !is_null($this->user);
    }

    /**
     * @inheritDoc
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

    public function logout(): JsonResponse
    {
        $this->user = null;
        $this->token = null;

        $response = response()->json(null, 401);

        try {
            $request = resolve(Request::class);

            $domain = implode('.', array_slice(explode('.', $request->getHttpHost()), -2));

            $response->headers->clearCookie(Token::COOKIE_AUTHORIZED, '/', $domain);
            $response->headers->clearCookie(Token::COOKIE_ACCESS, '/', $domain);
            $response->headers->clearCookie(
                Token::COOKIE_REFRESH,
                route('auth.token.refresh', [], false),
                $request->getHttpHost()
            );
        } catch (Throwable $e) {
            // Call from console?
        }

        return $response->send();
    }
}
