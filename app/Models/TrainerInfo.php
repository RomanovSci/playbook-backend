<?php

namespace App\Models;

/**
 * Class TrainerInfo
 *
 * @package App\Models
 *
 * @OA\Schema(
 *      schema="TrainerInfo",
 *      required={
 *          "user_id",
 *          "min_price",
 *          "max_price",
 *          "currency"
 *      },
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="user_id",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="about",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="min_price",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="max_price",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="currency",
 *          type="string",
 *          minLength=3,
 *          maxLength=3
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          type="string",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="deleted_at",
 *          type="string",
 *          readOnly=true
 *      )
 * )
 */
class TrainerInfo extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'trainers_info';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'about', 'min_price',
        'max_price', 'currency'
    ];
}
