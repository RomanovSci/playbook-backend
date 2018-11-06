<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Booking
 *
 * @package App\Models
 *
 * @OA\Schema(
 *      schema="Booking",
 *      required={
 *          "bookable_id",
 *          "bookable_type",
 *          "start_time",
 *          "end_time"
 *      },
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="bookable_id",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="bookable_type",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="start_time",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="end_time",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="integer"
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
class Booking extends Model
{
    use SoftDeletes;

    protected $table = 'bookings';

    /**
     * Bookable entities
     *
     * @return MorphTo
     */
    public function bookable()
    {
        return $this->morphTo();
    }
}
