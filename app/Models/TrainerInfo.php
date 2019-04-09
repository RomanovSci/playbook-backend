<?php

namespace App\Models;

/**
 * Class TrainerInfo
 * @package App\Models
 *
 * @property integer user_uuid
 * @property string about
 * @property integer min_price
 * @property integer max_price
 * @property string currency
 *
 * @property File[] images
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="user_uuid",
 *                  type="string",
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
    protected $casts = [
        'min_price' => 'integer',
        'max_price' => 'integer',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_uuid',
        'about',
        'min_price',
        'max_price',
        'currency',
    ];

    /**
     * @var array
     */
    protected $with = [
        'images'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany(File::class, 'entity', null, 'entity_uuid');
    }
}
