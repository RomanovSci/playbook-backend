<?php

namespace App\Services;

use App\Exceptions\Internal\IncorrectDateRange;
use App\Helpers\DateTimeHelper;
use App\Helpers\ScheduleHelper;
use App\Models\Schedule\Schedule;
use App\Models\SchedulePlayground;
use App\Models\User;
use App\Objects\Service\ExecResult;
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
     * @return ExecResult
     *
     * @throws \Throwable
     */
    public static function create(Model $schedulable, array $data): ExecResult
    {
        $schedules = [];
        $data['price_per_hour'] = money($data['price_per_hour'], $data['currency'])->getAmount();

        DB::beginTransaction();
        try {
            foreach ($data['dates'] as $date) {
                $startTime = Carbon::parse($date['start_time']);
                $endTime = Carbon::parse($date['end_time']);

                /**
                 * Check if time periods is overlaps
                 */
                if (ScheduleHelper::periodsIsOverlaps($schedulable, $startTime, $endTime)) {
                    throw new IncorrectDateRange(__('errors.schedule_already_exists'));
                }

                /**
                 * @var SchedulePlayground[] $playgrounds
                 * @var Schedule $schedule
                 */
                $playgrounds = [];
                $schedule = Schedule::create(array_merge($data, [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'schedulable_uuid' => $schedulable->uuid,
                    'schedulable_type' => get_class($schedulable)
                ]));

                if ($schedule instanceof User) {
                    foreach ($data['playgrounds'] as $playgroundUuid) {
                        $playgrounds[] = SchedulePlayground::create([
                            'playground_uuid' => $playgroundUuid,
                            'schedule_uuid' => $schedule->uuid,
                        ]);
                    }
                }

                $schedules[] = $schedule->fresh()->toArray();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw $e;
        }

        return ExecResult::instance()
            ->setSuccess()
            ->setData(['schedules' => $schedules]);
    }

    /**
     * Edit schedule
     *
     * @param Schedule $schedule
     * @param array $data
     * @return ExecResult
     *
     * @throws IncorrectDateRange
     */
    public static function edit(Schedule $schedule, array $data): ExecResult
    {
        $newStartTime = Carbon::parse($data['start_time']);
        $newEndTime = Carbon::parse($data['end_time']);

        if (ScheduleHelper::periodsIsOverlaps($schedule->schedulable, $newStartTime, $newEndTime, [$schedule])) {
            throw new IncorrectDateRange();
        }

        $schedule->fill($data)->update();

        return ExecResult::instance()
            ->setSuccess()
            ->setData(['schedule' => $schedule]);
    }
}
