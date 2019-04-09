<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 * @package App\Http\Controllers
 *
 * @OA\Info(
 *     title="Playbook API documentation",
 *     version="0.0.1"
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
     * @return JsonResponse
     */
    protected function success($data = [], $message = null): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message ?? 'Success',
            'data' => $data,
        ];

        return response()->json($response);
    }

    /**
     * Error response
     *
     * @param null $message
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    protected function error($message = null, $data = [], int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message ?? 'Ooops...Something went wrong',
            'data' => $data
        ];

        return response()->json($response, $code);
    }
}
