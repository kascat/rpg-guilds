<?php

namespace Sessions;

use Carbon\Carbon;
use Guilds\Guild;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Users\User;

/**
 * Class Session
 * @property int          $id
 * @property int          $user_id
 * @property string       $name
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 *
 * @property-read User    $user
 * @property-read Guild[]|Collection $guilds
 */
class Session extends Model
{
    const ID = 'id';
    const USER_ID = 'user_id';
    const NAME = 'name';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const RELATION_USER = 'user';
    const RELATION_GUILDS = 'guilds';

    const TABLE = 'sessions';

    protected $table = self::TABLE;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID, User::ID);
    }

    public function guilds(): HasMany
    {
        return $this->hasMany(Guild::class, Guild::SESSION_ID, self::ID);
    }
}
