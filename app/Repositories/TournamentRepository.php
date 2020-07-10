<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TournamentRepository
 * @package App\Repositories
 */
class TournamentRepository extends Repository
{
    protected const MODEL = Tournament::class;

    /**
     * @param array $data
     * @return Collection
     */
    public function get(array $data): Collection
    {
        $query = $this->builder()->limit($data['limit'])->offset($data['offset']);

        if (isset($data['query'])) {
            $query->orWhere('title', 'ilike', '%' . $data['query'] . '%')
                ->orWhere('description', 'ilike', '%' . $data['query'] . '%')
                ->orWhere('sport', 'ilike', '%' . $data['query'] . '%')
                ->orWhere('category', 'ilike', '%' . $data['query'] . '%');
        }

        return $query->get();
    }
}
