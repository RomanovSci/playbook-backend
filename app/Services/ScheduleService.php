<?php

namespace App\Services;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ScheduleCreatorService
 *
 * @package App\Services\Schedule
 */
class ScheduleService
{
    /**
     * Create schedules for schedulable entity
     *
     * @param Model $schedulable
     * @param array $data
     * @return array
     */
    public function create(Model $schedulable, array $data): array
    {
        $schedules = [];
        $data['price_per_hour'] = money($data['price_per_hour'], $data['currency'])->getAmount();

        foreach ($data['dates'] as $index => $date) {
            /**
             * @var Schedule $trainerSchedule
             */
            $trainerSchedule = Schedule::create(array_merge($data, [
                'start_time' => $date . ' ' . $data['start_time'],
                'end_time' => $date . ' ' . $data['end_time'],
                'schedulable_id' => $schedulable->id,
                'schedulable_type' => get_class($schedulable)
            ]));
            $schedules[] = $trainerSchedule->toArray();
        }

        return $schedules;
    }
}
