<?php

namespace App\Models;

/**
 * Class Country
 * @package App\Models
 *
 * @property string code
 * @property string name
 * @property string origin_name
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "code",
 *                  "name",
 *                  "origin_name"
 *              },
 *              @OA\Property(
 *                  property="name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="dial_code",
 *                  type="string",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Country extends BaseModel
{
    protected $table = 'countries';
}
