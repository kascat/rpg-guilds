<?php

namespace Players;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Players\Enums\PlayerClassesEnum;
use Users\User;

/**
 * Class Player
 * @property int               $id
 * @property int               $user_id
 * @property string            $name
 * @property PlayerClassesEnum $class
 * @property int               $xp
 * @property bool              $active
 * @property Carbon|null       $created_at
 * @property Carbon|null       $updated_at
 *
 * @property-read User         $user
 */
class Player extends Model
{
    const ID = 'id';
    const USER_ID = 'user_id';
    const NAME = 'name';
    const PLAYER_CLASS = 'class';
    const XP = 'xp';
    const ACTIVE = 'active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const RELATION_USER = 'user';

    const TABLE = 'players';

    protected $table = self::TABLE;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::PLAYER_CLASS => PlayerClassesEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID, User::ID);
    }
}
