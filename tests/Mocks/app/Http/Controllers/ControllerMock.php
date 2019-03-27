<?php

namespace Tests\Mocks\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Class ControllerMock
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
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    public function error($message = null, $data = [], int $code = 400): JsonResponse
    {
        return parent::error($message, $data, $code);
    }
}
