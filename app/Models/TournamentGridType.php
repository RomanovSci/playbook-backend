<?php

namespace App\Models;

/**
 * Class TournamentGridType
 * @package App\Models
 *
 * @property string name
 * @property string description
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
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel")
 *      }
 * )
 */
class TournamentGridType extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'tournaments_grids_types';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];
}
