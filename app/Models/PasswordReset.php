<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PasswordReset
 * @package App\Models
 *
 * @property string user_uuid
 * @property string reset_code
 *
 * @property string expired_at
 * @property string used_at
 *
 * @property User user
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="user_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="reset_code",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="expired_at",
 *                  type="string",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class PasswordReset extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'password_resets';

    /**
     * @var array
     */
    protected $fillable = [
        'user_uuid',
        'reset_code',
        'expired_at',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'expired_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * PasswordReset constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->hidden = array_merge($this->hidden, ['used_at']);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
