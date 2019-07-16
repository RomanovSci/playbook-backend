<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class Tournament
 * @package App\Models
 *
 * @property string name
 * @property string description
 * @property string tournament_type_uuid
 * @property string creator_uuid
 * @property integer challonge_id
 * @property string started_at
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="description",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="is_private",
 *                  type="boolean",
 *              ),
 *              @OA\Property(
 *                  property="start_time",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="end_time",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="max_participants_count",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="registration_start_time",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="price",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="currency",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="tournament_type_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="tournament_grid_type_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="creator_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="challonge_id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="started_at",
 *                  type="string",
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel")
 *      }
 * )
 */
class Tournament extends BaseModel
{
    /**
     * @var array
     */
    protected $casts = [
        'challonge_id' => 'integer',
        'is_private' => 'boolean',
        'max_participants_count' => 'integer',
        'price' => 'integer',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'is_private',
        'start_time',
        'end_time',
        'registration_start_time',
        'max_participants_count',
        'price',
        'currency',
        'tournament_type_uuid',
        'tournament_grid_type_uuid',
        'creator_uuid',
        'challonge_id',
    ];

    /**
     * @var string
     */
    protected $table = 'tournaments';
}
