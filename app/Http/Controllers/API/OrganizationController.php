<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\OrganizationCreateFormRequest;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class OrganizationController
 *
 * @package App\Http\Controllers\API
 */
class OrganizationController extends Controller
{
    /**
     * Create organization
     *
     * @param OrganizationCreateFormRequest $request
     * @return JsonResponse
     */
    public function create(OrganizationCreateFormRequest $request)
    {
        /**
         * @var Organization $organization
         */
        $organization = Organization::create(array_merge(
            $request->all(),
            ['owner_id' => Auth::user()->id]
        ));

        return $this->success($organization->toArray());
    }
}
