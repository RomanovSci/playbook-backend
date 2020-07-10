<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class TournamentPlayer
 * @package App\Models
 *
 * @property string tournament_uuid
 * @property string user_uuid
 * @property string first_name
 * @property string last_name
 * @property integer order
 */
class TournamentPlayer extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'tournaments_players';

    /**
     * @var array
     */
    protected $casts = ['order'];

    /**
     * @var array
     */
    protected $fillable = [
        'tournament_uuid',
        'user_uuid',
        'first_name',
        'last_name',
        'order',
    ];
}
