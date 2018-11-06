<?php

namespace App\Repositories;

use App\Models\Playground;

/**
 * Class PlaygroundRepository
 *
 * @package App\Repositories
 */
class PlaygroundRepository
{
    /**
     * Fetch all playgrounds
     *
     * @return Playground[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAll(): array
    {
        return Playground::all()->toArray();
    }
}
