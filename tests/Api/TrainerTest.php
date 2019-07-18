<?php
declare(strict_types = 1);

namespace Tests\Api;

use Illuminate\Http\Response;
use Tests\ApiTestCase;

/**
 * Class TrainerTest
 * @package Tests\Api
 */
class TrainerTest extends ApiTestCase
{
    /**
     * @return void
     */
    public function testGetTrainerListSuccess(): void
    {
        $this->call('GET', route('trainer.get'), ['limit' => 1, 'offset' => 1,])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                'total_count' => null,
                'list' => []
            ]));
    }

    /**
     * @return void
     */
    public function testGetTrainerListValidationError(): void
    {
        $this->call('GET', route('trainer.get'))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'offset' => [],
                'limit' => [],
            ]));
    }
}
