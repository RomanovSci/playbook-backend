<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

/**
 * Class PlaygroundController
 *
 * @package App\Http\Controllers\API
 */
class PlaygroundController extends Controller
{
    /**
     * Create playground
     *
     * @return string
     */
    public function create()
    {
        //TODO: Create playground

        return response()->json([
            'success' => true,
        ]);
    }
}
