<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 *
 * @package App\Http\Controllers
 * @OA\Info(
 *     title="ISport API documentation",
 *     version="0.1"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Simple success response
     *
     * @param null $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = [], $message = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message ?? 'Success',
            'data' => $data,
        ]);
    }

    /**
     * Simple forbidden response
     *
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function forbidden($message = null): JsonResponse
    {
        return response()->json([
            'message' => $message ?? 'Forbidden',
        ], 403);
    }
}
