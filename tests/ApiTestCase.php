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
        return [
            'success' => true,
            'message' => 'Success',
            'data' => $data
        ];
    }

    /**
     * @return array
     */
    protected function unauthorizedResponse(): array
    {
        return [
            'success' => false,
            'message' => 'Unauthorized',
        ];
    }

    /**
     * @param array $data
     * @param string $message
     * @return array
     */
    protected function errorResponse(array $data = [], string $message = 'Validation error'): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];
    }
}
