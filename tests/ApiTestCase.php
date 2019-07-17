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
}
