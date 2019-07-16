<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class Language
 * @package App\Models
 *
 * @property string code
 * @property string name
 * @property string native_name
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="code",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="native_name",
 *                  type="string",
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel")
 *      }
 * )
 */
class Language extends BaseModel
{
    protected $table = 'languages';
}
