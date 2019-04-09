<?php

namespace App\Services\Schedule;

use App\Exceptions\Internal\IncorrectDateRange;
use App\Helpers\ScheduleHelper;
use App\Models\Schedule\Schedule;
use App\Models\SchedulePlayground;
use App\Models\User;
use App\Services\ExecResult;
use Carbon\Carbon;

/**
 * Class EditScheduleService
 * @package App\Services\Schedule
 */
class EditScheduleService
{
    /**
     * Edit schedule
     *
     * @param Schedule $schedule
     * @param array $data
     * @return ExecResult
     *
     * @throws IncorrectDateRange
     */
    public function run(Schedule $schedule, array $data): ExecResult
    {
        $newStartTime = Carbon::parse($data['start_time']);
        $newEndTime = Carbon::parse($data['end_time']);

        if (ScheduleHelper::periodsIsOverlaps($schedule->schedulable, $newStartTime, $newEndTime, [$schedule])) {
            throw new IncorrectDateRange();
        }

        $schedule->fill($data)->update();

        if ($schedule->schedulable instanceof User) {
            $schedule->playgrounds()->detach();
            foreach ($data['playgrounds'] as $playgroundUuid) {
                $playgrounds[] = SchedulePlayground::create([
                    'playground_uuid' => $playgroundUuid,
                    'schedule_uuid' => $schedule->uuid,
                ]);
            }
        }

        return ExecResult::instance()
            ->setSuccess()
            ->setData(['schedule' => $schedule->refresh()]);
    }
}
