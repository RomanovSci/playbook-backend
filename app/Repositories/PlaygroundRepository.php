<?php

namespace App\Repositories;

use App\Models\Playground;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PlaygroundRepository
 * @package App\Repositories
 */
class PlaygroundRepository
{
    /**
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public static function get(int $limit, int $offset): Collection
    {
        return self::query($limit, $offset)->get();
    }

    /**
     * Search playgrounds
     *
     * @param array $data
     * @return Collection
     */
    public static function search(array $data): Collection
    {
        return self::query($data['limit'], $data['offset'])
            ->where('name', 'ilike', '%' . $data['query'] . '%')
            ->orWhere('description', 'ilike', '%' . $data['query'] . '%')
            ->orWhere('address', 'ilike', '%' . $data['query'] . '%')
            ->get();
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    protected static function query(int $limit, int $offset)
    {
        return Playground::limit($limit)->offset($offset);
    }
}
