<?php

namespace App\Models;

/**
 * Class TrainerInfo
 * @package App\Models
 *
 * @property integer id
 * @property integer user_id
 * @property string about
 * @property integer min_price
 * @property integer max_price
 * @property string currency
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "user_id",
 *                  "min_price",
 *                  "max_price",
 *                  "currency"
 *              },
 *              @OA\Property(
 *                  property="id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="user_id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="about",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="min_price",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="max_price",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="currency",
 *                  type="string",
 *                  minLength=3,
 *                  maxLength=3,
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel")
 *      }
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
