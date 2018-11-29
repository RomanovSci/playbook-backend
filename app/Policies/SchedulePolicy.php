<?php

namespace App\Policies;

use App\Models\Playground;
use App\Models\Schedule;
use App\Models\User;

/**
 * Class SchedulePolicy
 *
 * @package App\Policies
 */
class SchedulePolicy
{
    /**
     * Determine if the playground can be created by user
     *
     * @param User $user
     * @param Schedule $schedule
     * @return bool
     */
    public function createBooking(User $user, Schedule $schedule): bool
    {
        /** Anybody can book playground */
        if ($schedule->schedulable_type === Playground::class) {
            return true;
        }

        /** Trainer can't book himself */
        return $user->id !== $schedule->schedulable_id;
    }
}
