<?php
declare(strict_types = 1);

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TournamentPlayer
 * @package App\Models
 *
 * @property string tournament_uuid
 * @property string user_uuid
 * @property string first_name
 * @property string last_name
 * @property integer order
 *
 * @property Tournament tournament
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="tournament_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="user_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="first_name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="last_name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="order",
 *                  type="integer",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
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
    protected $casts = ['order' => 'integer'];

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

    /**
     * @return BelongsTo
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
