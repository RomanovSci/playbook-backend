<?php
declare(strict_types = 1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
        return $this->response(Response::HTTP_OK, $data, $message ?? 'Success');
    }

    /**
     * Created response
     *
     * @param array $data
     * @param null $message
     * @return JsonResponse
     */
    protected function created($data = [], $message = null): JsonResponse
    {
        return $this->response(Response::HTTP_CREATED, $data, $message ?? 'Created');
    }

    /**
     * Error response
     *
     * @param null $message
     * @param array $data
     * @return JsonResponse
     */
    protected function error($message = null, $data = []): JsonResponse
    {
        return $this->response(Response::HTTP_BAD_REQUEST, $data, $message ?? 'Ooops...Something went wrong');
    }

    /**
     * @param int $code
     * @param array $data
     * @param string $message
     * @return JsonResponse
     */
    protected function response(int $code, $data = [], $message = ''): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
