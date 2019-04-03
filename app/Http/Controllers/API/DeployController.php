<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class DeployController
 * @package App\Http\Controllers\API
 */
class DeployController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function front(Request $request)
    {
        $xGitLabToken = $request->header('X-Gitlab-Token');

        if ($xGitLabToken === env('X_GITLAB_TOKEN')) {
            return $this->success([
                '1' => $xGitLabToken,
                '2' => env('X_GITLAB_TOKEN'),
            ]);
        }

        return $this->error([
            '1' => $xGitLabToken,
            '2' => env('X_GITLAB_TOKEN'),
        ]);
    }
}
