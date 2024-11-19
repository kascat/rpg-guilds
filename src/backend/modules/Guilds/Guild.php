<?php

namespace Guilds;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Players\Player;
use Sessions\Session;
use Users\User;

/**
 * Class Guild
 * @property int          $id
 * @property int          $user_id
 * @property int          $session_id
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 *
 * @property-read User    $user
 * @property-read Session $session
 * @property-read Player[] $players
 */
class Guild extends Model
{
    const ID = 'id';
    const USER_ID = 'user_id';
    const SESSION_ID = 'session_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const RELATION_USER = 'user';
    const RELATION_SESSION = 'session';
    const RELATION_PLAYERS = 'players';

    const TABLE = 'guilds';

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

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, self::SESSION_ID, Session::ID);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Player::class,
            table: 'guild_players',
            foreignPivotKey: 'guild_id',
            relatedPivotKey: 'player_id',
            parentKey: self::ID,
            relatedKey: Player::ID
        );
    }
}
