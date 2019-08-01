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
