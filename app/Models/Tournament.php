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
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'tournament_type_uuid',
        'creator_uuid',
        'challonge_id'
    ];

    /**
     * @var string
     */
    protected $table = 'tournaments';
}
