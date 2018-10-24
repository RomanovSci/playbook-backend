<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Playground\Create as PlaygroundCreateRequest;

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
     * @param PlaygroundCreateRequest $request
     * @return string
     */
    public function create(PlaygroundCreateRequest $request)
    {
        return $this->success();
    }
}
