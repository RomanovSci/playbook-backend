<?php

namespace App\Repositories;
use App\Models\PlaygroundType;
use Illuminate\Support\Collection;

/**
 * Class PlaygroundTypesRepository
 * @package App\Repositories
 */
class PlaygroundTypesRepository
{
    /**
     * Get all playground types
     *
     * @return Collection
     */
    public static function all(): Collection
    {
        return PlaygroundType::all();
    }
}
