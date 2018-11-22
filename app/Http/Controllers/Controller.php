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
 *     title="Playbook API documentation",
 *     version="0.1"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const CODE_UNAUTHORIZED = 401;
    const CODE_FORBIDDEN = 403;

    /**
     * Simple success response
     *
     * @param null $message
     * @param array $data
     * @return JsonResponse
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
     * @return JsonResponse
     */
    protected function forbidden($message = null): JsonResponse
    {
        return $this->error(self::CODE_FORBIDDEN, [], $message ?? 'Forbidden');
    }

    /**
     * Simple unauthorized response
     *
     * @param null $message
     * @return JsonResponse
     */
    protected function unauthorized($message = null): JsonResponse
    {
        return $this->error(self::CODE_UNAUTHORIZED, [], $message ?? 'Unauthorized');
    }

    /**
     * Error response
     *
     * @param int $code
     * @param array $data
     * @param null $message
     * @return JsonResponse
     */
    protected function error(int $code, $data = [], $message = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message ?? 'Ooops...Something went wrong',
            'data' => $data
        ], $code);
    }
}
