<?php
declare(strict_types = 1);

namespace App\Services\Schedule;

use App\Exceptions\Internal\IncorrectDateRange;
use App\Models\Schedule;
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
     * @var ScheduleTimingService
     */
    protected $scheduleTimingService;

    /**
     * @param ScheduleTimingService $scheduleTimingService
     */
    public function __construct(ScheduleTimingService $scheduleTimingService)
    {
        $this->scheduleTimingService = $scheduleTimingService;
    }

    /**
     * @param Schedule $schedule
     * @param array $data
     * @return ExecResult
     *
     * @throws IncorrectDateRange
     */
    public function edit(Schedule $schedule, array $data): ExecResult
    {
        $newStartTime = Carbon::parse($data['start_time']);
        $newEndTime = Carbon::parse($data['end_time']);

        if ($this->scheduleTimingService->scheduleExistsForPeriod(
            $schedule->schedulable,
            $newStartTime,
            $newEndTime,
            [$schedule]
        )) {
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
