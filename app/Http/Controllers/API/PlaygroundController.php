<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Playground\PlaygroundCreateFormRequest;
use App\Models\Organization;
use App\Models\Playground;
use App\Repositories\PlaygroundRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class PlaygroundController
 *
 * @package App\Http\Controllers\API
 */
class PlaygroundController extends Controller
{
    /**
     * Get all playgrounds
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        return $this->success(
            PlaygroundRepository::getAll()
        );
    }

    /**
     * Create playground
     *
     * @param Organization $organization
     * @param PlaygroundCreateFormRequest $request
     * @return string
     */
    public function create(
        Organization $organization,
        PlaygroundCreateFormRequest $request
    ) {
        if (Auth::user()->cant('createPlayground', $organization)) {
            return $this->forbidden();
        }

        /**
         * @var Playground $playground
         */
        $playground = Playground::create(array_merge(
            $request->all(),
            ['organization_id' => $organization->id]
        ));

        return $this->success($playground->toArray());
    }
}
