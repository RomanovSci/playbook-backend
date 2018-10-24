<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

/**
 * Class OrganizationController
 *
 * @package App\Http\Controllers\API
 */
class OrganizationController extends Controller
{
    /**
     * Create organization
     */
    public function create()
    {
        return $this->success();
    }
}
