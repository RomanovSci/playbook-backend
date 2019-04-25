<?php

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
 *                  property="start_date",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="end_date",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="max_participants_count",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="start_registration_date",
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
 *                  property="creator_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="challonge_id",
 *                  type="integer",
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
        'start_date',
        'end_date',
        'start_registration_date',
        'max_participants_count',
        'price',
        'currency',
        'tournament_type_uuid',
        'creator_uuid',
        'challonge_id',
    ];

    /**
     * @var string
     */
    protected $table = 'tournaments';
}
