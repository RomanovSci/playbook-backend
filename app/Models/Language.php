<?php

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
 *              required={
 *                  "code",
 *                  "name",
 *                  "native_name",
 *              },
 *              @OA\Property(
 *                  property="id",
 *                  type="integer",
 *              ),
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
