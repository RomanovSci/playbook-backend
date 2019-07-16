<?php
declare(strict_types = 1);

namespace App\Policies;

use App\Models\Playground;
use App\Models\Schedule;
use App\Models\User;

/**
 * Class SchedulePolicy
 * @package App\Policies
 */
class SchedulePolicy
{
    /**
     * Determine if user can manage schedule
     *
     * @param User $user
     * @param Schedule $schedule
     * @return bool
     */
    public function manageSchedule(User $user, Schedule $schedule): bool
    {
        /** Trainer can manage own schedule */
        if ($schedule->schedulable_type === User::class && $schedule->schedulable_uuid === $user->uuid) {
            return true;
        }

        /** Playground schedule creator can manage playground schedule */
        if ($schedule->schedulable_type === Playground::class && $schedule->schedulable->creator_uuid === $user->uuid) {
            return true;
        }

        return false;
    }
}
