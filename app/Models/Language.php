<?php

namespace App\Models;

/**
 * Class Language
 * @package App\Models
 *
 * @property string code
 * @property string name
 * @property string native_name
 */
class Language extends BaseModel
{
    protected $table = 'languages';
}
