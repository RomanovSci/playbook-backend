<?php

namespace App\Models;

/**
 * Class Country
 *
 * @package App\Models
 * @property int id
 * @property string code
 * @property string name
 * @property string origin_name
 *
 * @OA\Schema(
 *      schema="Country",
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "code",
 *                  "name",
 *                  "origin_name"
 *              },
 *              @OA\Property(
 *                  property="id",
 *                  type="integer",
 *                  readOnly=true
 *              ),
 *              @OA\Property(
 *                  property="name",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="dial_code",
 *                  type="string"
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
