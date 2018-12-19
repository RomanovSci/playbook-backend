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
     * Determine if the booking can be created by user
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

    /**
     * Determine if user can delete schedule
     *
     * @param User $user
     * @param Schedule $schedule
     * @return bool
     */
    public function deleteSchedule(User $user, Schedule $schedule): bool
    {
        /** Trainer can delete own schedule */
        if ($schedule->schedulable_type === User::class && $schedule->schedulable_id === $user->id) {
            return true;
        }

        /** Playground schedule creator can delete playground schedule */
        if ($schedule->schedulable_type === Playground::class && $schedule->schedulable()->creator_id === $user->id) {
            return true;
        }

        return false;
    }
}
