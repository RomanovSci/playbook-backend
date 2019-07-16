<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TournamentRequest
 * @package App\Models
 *
 * @property string tournament_uuid
 * @property string user_uuid
 * @property string approved_at
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
 *                  property="approved_at",
 *                  type="string",
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel")
 *      }
 * )
 */
class TournamentRequest extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'tournaments_requests';

    /**
     * @var array
     */
    protected $fillable = [
        'tournament_uuid',
        'user_uuid',
    ];

    /**
     * TournamentRequest constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->hidden = array_merge($this->hidden, ['tournament']);
    }

    /**
     * @return BelongsTo
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
