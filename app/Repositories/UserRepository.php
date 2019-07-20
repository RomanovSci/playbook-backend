<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class UserRepository
 * @package App\Repositories
 */
class UserRepository
{
    /**
     * Get users list by role
     *
     * @param string $role
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public static function getByRole(string $role, int $limit, int $offset): Collection
    {
        return User::role($role)
            ->with('trainerInfo')
            ->active()
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    /**
     * Get users count by role
     *
     * @param string $role
     * @return int
     */
    public static function getCountByRole(string $role): int
    {
        return User::role($role)->active()->count();
    }

    /**
     * Get user by phone
     *
     * @param string $phone
     * @return User
     */
    public static function getByPhone(string $phone): User
    {
        return User::where('phone', $phone)->firstOrFail();
    }

    /**
     * Get user by uuid
     *
     * @param string $uuid
     * @return User
     */
    public static function getByUuid(string $uuid): User
    {
        return User::where('uuid', $uuid)->firstOrFail();
    }
}
