<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Domains\Auth\Jobs\RevokeTokensBySessionIdJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lucid\Units\Feature;

class LogoutFeature extends Feature
{
    public function handle(Request $request): mixed
    {
        $token = Auth::token();

        $this->run(RevokeTokensBySessionIdJob::class, [
            'sessionId' => $token->sid,
        ]);

        return response()->json(null, 204);
    }
}
