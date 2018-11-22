<?php

namespace Tests\Mocks\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Class ControllerMock
 *
 * @package Tests\Mocks\app\Http\Controllers
 */
class ControllerMock extends Controller
{
    /**
     * @inheritdoc
     * @param array $data
     * @param null $message
     * @return JsonResponse
     */
    public function success($data = [], $message = null): JsonResponse
    {
        return parent::success($data, $message);
    }

    /**
     * @inheritdoc
     * @param null $message
     * @return JsonResponse
     */
    public function forbidden($message = null): JsonResponse
    {
        return parent::forbidden($message);
    }

    /**
     * @inheritdoc
     * @param null $message
     * @return JsonResponse
     */
    public function unauthorized($message = null): JsonResponse
    {
        return parent::unauthorized($message);
    }

    /**
     * @inheritdoc
     * @param int $code
     * @param array $data
     * @param null $message
     * @return JsonResponse
     */
    public function error(int $code, $data = [], $message = null): JsonResponse
    {
        return parent::error($code, $data, $message);
    }
}
