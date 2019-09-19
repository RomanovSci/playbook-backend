<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Playground;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PlaygroundRepository
 * @package App\Repositories
 */
class PlaygroundRepository extends Repository
{
    /**
     * @param array $data
     * @return Collection
     */
    public function get(array $data): Collection
    {
        /** @var Builder $query */
        $query = Playground::limit($data['limit'])->offset($data['offset']);

        if (isset($data['query'])) {
            $query->where('name', 'ilike', '%' . $data['query'] . '%')
                ->orWhere('description', 'ilike', '%' . $data['query'] . '%')
                ->orWhere('address', 'ilike', '%' . $data['query'] . '%');
        }

        return $query->orderBy('created_at', 'DESC')->get();
    }
}
