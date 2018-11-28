<?php

namespace App\Models;

/**
 * Class Playground
 *
 * @package App\Models
 * @property integer id
 * @property string name
 * @property string description
 * @property string address
 * @property \DateTime opening_time
 * @property \DateTime closing_time
 * @property integer type_id
 * @property integer organization_id
 * @property Organization organization
 *
 * @OA\Schema(
 *      schema="Playground",
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "type_id",
 *                  "name",
 *                  "description",
 *                  "address",
 *                  "opening_time",
 *                  "closing_time",
 *
 *              },
 *              @OA\Property(
 *                  property="id",
 *                  type="integer",
 *                  readOnly=true
 *              ),
 *              @OA\Property(
 *                  property="type_id",
 *                  type="integer"
 *              ),
 *              @OA\Property(
 *                  property="organization_id",
 *                  type="integer"
 *              ),
 *              @OA\Property(
 *                  property="name",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="description",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="address",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="opening_time",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="closing_time",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="status",
 *                  type="integer",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Playground extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'playgrounds';

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'address',
        'opening_time', 'closing_time', 'type_id',
        'organization_id',
    ];

    /**
     * @var array
     */
    protected $with = [
        'organization',
        'type',
        'schedules',
    ];

    /**
     * Get type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(PlaygroundType::class);
    }

    /**
     * Get organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get schedules
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function schedules()
    {
        return $this->morphMany(Schedule::class, 'schedulable');
    }
}
