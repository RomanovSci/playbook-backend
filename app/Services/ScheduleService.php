<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\SchedulePlayground;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        DB::beginTransaction();
        try {
            foreach ($data['dates'] as $index => $date) {
                /**
                 * @var SchedulePlayground[] $playgrounds
                 * @var Schedule $trainerSchedule
                 */
                $playgrounds = [];
                $trainerSchedule = Schedule::create(array_merge($data, [
                    'start_time' => $date . ' ' . $data['start_time'],
                    'end_time' => $date . ' ' . $data['end_time'],
                    'schedulable_id' => $schedulable->id,
                    'schedulable_type' => get_class($schedulable)
                ]));

                foreach ($data['playgrounds'] as $playgroundId) {
                    $playgrounds[] = SchedulePlayground::create([
                        'playground_id' => $playgroundId,
                        'schedule_id' => $trainerSchedule->id,
                    ]);
                }

                $schedules[] = $trainerSchedule->fresh()->toArray();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return [];
        }

        return $schedules;
    }
}
