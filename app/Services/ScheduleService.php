<?php

namespace App\Services;

use App\Exceptions\Internal\IncorrectScheduleDateRange;
use App\Helpers\DateTimeHelper;
use App\Models\Schedule;
use App\Models\SchedulePlayground;
use App\Repositories\ScheduleRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ScheduleCreatorService
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
     *
     * @throws IncorrectScheduleDateRange
     * @throws \Throwable
     */
    public function create(Model $schedulable, array $data): array
    {
        $schedules = [];
        $data['price_per_hour'] = money($data['price_per_hour'], $data['currency'])->getAmount();

        DB::beginTransaction();
        try {
            foreach ($data['dates'] as $index => $date) {
                $startTime = Carbon::parse($date . ' ' . $data['start_time']);
                $endTime = Carbon::parse($date . ' ' . $data['end_time']);

                /**
                 * Check if range is negative
                 */
                if ($startTime->greaterThanOrEqualTo($endTime)) {
                    throw new IncorrectScheduleDateRange('Range is negative');
                }

                /**
                 * Check if time periods is overlaps
                 */
                if ($this->periodsIsOverlaps($schedulable, $startTime, $endTime)) {
                    throw new IncorrectScheduleDateRange();
                }

                /**
                 * @var SchedulePlayground[] $playgrounds
                 * @var Schedule $schedule
                 */
                $playgrounds = [];
                $schedule = Schedule::create(array_merge($data, [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'schedulable_id' => $schedulable->id,
                    'schedulable_type' => get_class($schedulable)
                ]));

                foreach ($data['playgrounds'] as $playgroundId) {
                    $playgrounds[] = SchedulePlayground::create([
                        'playground_id' => $playgroundId,
                        'schedule_id' => $schedule->id,
                    ]);
                }

                $schedules[] = $schedule->fresh()->toArray();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            if ($e instanceof IncorrectScheduleDateRange) {
                throw $e;
            }

            return [];
        }

        return $schedules;
    }

    /**
     * Edit schedule
     *
     * @param Schedule $schedule
     * @param array $data
     * @return Schedule
     *
     * @throws IncorrectScheduleDateRange
     */
    public function edit(Schedule $schedule, array $data): Schedule
    {
        $newStartTime = Carbon::parse($data['start_time']);
        $newEndTime = Carbon::parse($data['end_time']);

        if ($this->periodsIsOverlaps($schedule->schedulable, $newStartTime, $newEndTime)) {
            throw new IncorrectScheduleDateRange();
        }

        return $schedule;
    }

    /**
     * Check periods overlaps
     *
     * @param Model $schedulable
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return bool
     *
     * @throws IncorrectScheduleDateRange
     */
    protected function periodsIsOverlaps(
        Model $schedulable,
        Carbon $startTime,
        Carbon $endTime
    ): bool {
        $existedSchedules = ScheduleRepository::getBySchedulable(
            get_class($schedulable),
            $schedulable->id
        );

        foreach ($existedSchedules as $existedSchedule) {
            $scheduleStartTime = Carbon::parse($existedSchedule->start_time);
            $scheduleEndTime = Carbon::parse($existedSchedule->end_time);

            if (DateTimeHelper::timePeriodsIsOverlaps($startTime, $endTime, $scheduleStartTime, $scheduleEndTime)) {
                return true;
            }
        }

        return false;
    }
}
