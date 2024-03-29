<?php
declare(strict_types = 1);

namespace App\Services\Schedule;

use App\Exceptions\Internal\IncorrectDateRange;
use App\Models\Schedule;
use App\Models\SchedulePlayground;
use App\Models\User;
use App\Services\ExecResult;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class CreateScheduleService
 * @package App\Services\Schedule
 */
class CreateScheduleService
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
     * @param Model $schedulable
     * @param array $data
     * @return ExecResult
     *
     * @throws \Throwable
     */
    public function create(Model $schedulable, array $data): ExecResult
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
                if ($this->scheduleTimingService->scheduleExistsForPeriod($schedulable, $startTime, $endTime)) {
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

                if ($schedulable instanceof User) {
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
}
