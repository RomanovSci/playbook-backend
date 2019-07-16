<?php
declare(strict_types = 1);

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
    public function getSchedules(): array
    {
        return $this->schedules;
    }

    /**
     * Add schedule to origin schedules
     *
     * @param Schedule $schedule
     * @return void
     */
    public function setSchedule(Schedule $schedule): void
    {
        $this->schedules[] = $schedule;
    }
}
