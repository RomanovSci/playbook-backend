<?php

namespace App\Models;

/**
 * Class Equipment
 * @package App\Models
 *
 * @property string creator_uuid
 * @property string name
 * @property integer price_per_hour
 * @property string currency
 * @property integer availability
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="creator_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="price_per_hour",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="currency",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="availability",
 *                  type="integer",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Equipment extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'equipments';

    /**
     * @var array
     */
    protected $fillable = [
        'creator_uuid',
        'name',
        'price_per_hour',
        'currency',
        'availability',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'availability' => 'integer',
        'price_per_hour' => 'integer',
    ];

    /**
     * Equipment constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->hidden = array_merge($this->hidden, [
            'creator_uuid',
            'created_at',
            'updated_at',
        ]);
    }
}
