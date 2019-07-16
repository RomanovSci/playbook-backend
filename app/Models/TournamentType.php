<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class TournamentType
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
class TournamentType extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'tournaments_types';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];
}
