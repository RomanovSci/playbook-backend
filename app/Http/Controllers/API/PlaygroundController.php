<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Playground\Create as PlaygroundCreateRequest;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

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
     * @param Organization $organization
     * @param PlaygroundCreateRequest $request
     * @return string
     */
    public function create(
        Organization $organization,
        PlaygroundCreateRequest $request
    ) {
        if (Auth::user()->cant('createPlayground', $organization)) {
            return $this->forbidden();
        }

        return $this->success();
    }
}
