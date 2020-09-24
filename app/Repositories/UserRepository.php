<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRepository
 * @package App\Repositories
 */
class UserRepository extends Repository
{
    protected const MODEL = User::class;

    /**
     * @param array $data
     * @return Collection
     */
    public function get(array $data): Collection
    {
        $query = $this->builder()->limit($data['limit'])->offset($data['offset']);

        if (isset($data['query'])) {
            $query->where('first_name', 'ilike', '%' . $data['query'] . '%')
                ->orWhere('last_name', 'ilike', '%' . $data['query'] . '%')
                ->orWhere('phone', 'ilike', '%' . $data['query'] . '%');
        }

        if (isset($data['uuid'])) {
            $query->where('uuid', $data['uuid']);
        }

        return $query->get();
    }

    /**
     * Get users list by role
     *
     * @param string $role
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public function getByRole(string $role, int $limit, int $offset): Collection
    {
        return $this->builder()
            ->role($role)
            ->with('trainerInfo')
            ->active()
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    /**
     * Get user by phone
     *
     * @param string $phone
     * @return User|Model
     */
    public function getByPhone(string $phone): User
    {
        return $this->builder()->where('phone', $phone)->firstOrFail();
    }

    /**
     * Get user by uuid
     *
     * @param string $uuid
     * @return User|Model
     */
    public function getByUuid(string $uuid): User
    {
        return $this->builder()->where('uuid', $uuid)->firstOrFail();
    }
}
