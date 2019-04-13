<?php

namespace App\Models;

/**
 * Class MergedSchedule
 * @package App\Models\Schedule
 */
class MergedSchedule extends Schedule
{
    /**
     * @var Schedule[]
     */
    protected $schedules = [];

    /**
     * Get origin schedules
     *
     * @return array
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * Add schedule to origin schedules
     *
     * @param Schedule $schedule
     */
    public function setSchedule(Schedule $schedule)
    {
        $this->schedules[] = $schedule;
    }
}
