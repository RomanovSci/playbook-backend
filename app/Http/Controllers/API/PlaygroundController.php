<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Playground\Create as PlaygroundCreate;

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
     * @param PlaygroundCreate $request
     * @return string
     */
    public function create(PlaygroundCreate $request)
    {
        return $this->success();
    }
}
