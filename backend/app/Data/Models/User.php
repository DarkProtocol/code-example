<?php

declare(strict_types=1);

namespace App\Data\Models;

use App\Common\Traits\AutoUUID;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property string $email
 * @property string $nickname
 * @property string $password
 * @property string|null $seed_phrase
 * @property string|null $ref_id
 * @property string $ref_level
 * @property string $avatar
 * @property int $role_id
 * @property string|null $create_ip
 * @property string|null $create_country
 * @property string|null $create_ua
 * @property bool $is_banned
 * @property int $wall_rating
 * @property string|null $ban_reason
 * @property string|null $banned_at
 * @property Carbon|null $activated_at
 * @property Carbon $created_at
 * @property Carbon $updated_at

 * @property User|null $referrer
 * @property Collection|null $activeMfaMethods
 * @property Collection|null $mfaMethods
 * @property Collection|null $chatSettings
 */
class User extends Authenticatable
{
    use AutoUUID;
    use HasFactory;
    use Notifiable;

    public const MIN_PASSWORD_LENGTH = 8;

    public const ROLE_USER = 0;
    public const ROLE_BOT = 1;
    public const ROLE_ADMIN = 10;
    public const ROLE_MODERATOR = 4;
    public const ROLE_DEVELOPER = 99;

    public const MODERATORS_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_MODERATOR,
        self::ROLE_DEVELOPER,
    ];

    public const ADMIN_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_DEVELOPER,
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    /** @var string[] */
    protected $fillable = [
        'email',
        'password',
        'nickname',
    ];

    /** @var string[] */
    protected $hidden = [
        'password',
        'create_ip',
        'create_country',
        'create_ua',
        'activated_at',
        'created_at',
        'updated_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'activated_at' => 'datetime',
        'banned_at' => 'datetime',
    ];

    public function referrer(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'ref_id');
    }
}
