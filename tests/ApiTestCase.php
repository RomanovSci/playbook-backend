<?php
declare(strict_types = 1);

namespace Tests;

use Illuminate\Support\Facades\Artisan;

/**
 * Class ApiTestCase
 * @package Tests
 */
class ApiTestCase extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('passport:install');
    }

    /**
     * @param array $data
     * @return array
     */
    protected function successResponse(array $data = []): array
    {
        return $this->response($data);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function createdResponse(array $data = []): array
    {
        return $this->response($data, 'Created');
    }

    /**
     * @return array
     */
    protected function unauthorizedResponse(): array
    {
        return ['message' => 'Unauthorized'];
    }

    /**
     * @param array $data
     * @param string $message
     * @return array
     */
    protected function errorResponse(array $data = [], string $message = 'Validation error'): array
    {
        return $this->response($data, $message);
    }

    /**
     * @param array $data
     * @param string $message
     * @return array
     */
    protected function response(array $data = [], string $message = 'Success'): array
    {
        return [
            'message' => $message,
            'data' => $data,
        ];
    }
}
