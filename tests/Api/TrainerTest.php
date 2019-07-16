<?php
declare(strict_types = 1);

namespace Tests\Api;

use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class TrainerTest
 * @package Tests\Api
 */
class TrainerTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetTrainerListSuccess(): void
    {
        $response = $this->call('GET', '/api/trainer', [
            'limit' => 1,
            'offset' => 1,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'success' => true,
            'message' => 'Success',
            'data' => [
                'total_count' => null,
                'list' => []
            ]
        ]);
    }

    /**
     * @return void
     */
    public function testGetTrainerListValidationError(): void
    {
        $response = $this->call('GET', '/api/trainer');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson([
            'success' => false,
            'message' => 'Validation error',
            'data' => [
                'offset' => [],
                'limit' => [],
            ]
        ]);
    }
}
