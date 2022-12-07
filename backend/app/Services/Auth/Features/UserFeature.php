<?php

declare(strict_types=1);

namespace App\Services\Auth\Features;

use App\Common\Support\Str;
use App\Data\Models\User;
use Illuminate\Http\Request;
use Lucid\Units\Feature;

class UserFeature extends Feature
{
    /**
     * @return array<string, mixed>
     */
    public function handle(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();

        return [
            'id' => $user->id,
            'email' => $user->email,
            'nickname' => $user->nickname,
            'level' => Str::formatReferralLevel($user->ref_level),
            'seedPhrase' => (bool) $user->seed_phrase,
            'avatar' => $user->avatar,
            'wallRating' => $user->wall_rating,
            'rating' => [
                'position' => $rating['user']['position'] ?? 0,
                'score' => Str::cleanDecimals((string)($rating['user']['score'] ?? '0')),
            ],
            'intercom' => [
               'hash' => hash_hmac(
                   'sha256',
                   $user->email,
                   env('INTERCOM_SECRET_KEY')
               ),
            ],
            'chats' => [],
        ];
    }
}
