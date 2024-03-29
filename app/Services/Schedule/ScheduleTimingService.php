<?php
declare(strict_types = 1);

namespace App\Services\Schedule;

use App\Exceptions\Internal\IncorrectDateRange;
use App\Helpers\DateTimeHelper;
use App\Models\Booking;
use App\Models\MergedSchedule;
use App\Models\Playground;
use App\Models\Schedule;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\ScheduleRepository;
use App\Services\ExecResult;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ScheduleTimingService
 * @package App\Services\Schedule
 */
class ScheduleTimingService
{
    /**
     * @var ScheduleRepository
     */
    protected $scheduleRepository;

    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * ScheduleTimingService constructor.
     *
     * @param ScheduleRepository $scheduleRepository
     * @param BookingRepository $bookingRepository
     */
    public function __construct(ScheduleRepository $scheduleRepository, BookingRepository $bookingRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Check if schedulable (trainer/playground) has schedules
     * that overlaps with period (startTime - endTime).
     * Exclude $excludedSchedules from the checking
     *
     * @param Model|User|Playground $schedulable
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param array $excludedSchedules
     * @return bool
     *
     * @throws IncorrectDateRange
     */
    public function scheduleExistsForPeriod(
        Model $schedulable,
        Carbon $startTime,
        Carbon $endTime,
        $excludedSchedules = []
    ): bool {
        $existedSchedules = $this->scheduleRepository->getBySchedulable(
            get_class($schedulable),
            $schedulable->uuid
        );

        /**
         * Exclude schedules that contains
         * in $excludedSchedules array
         */
        $existedSchedules = $existedSchedules->filter(
            function ($existedSchedule) use ($excludedSchedules) {
                foreach ($excludedSchedules as $excludedSchedule) {
                    if ($excludedSchedule->uuid === $existedSchedule->uuid) {
                        return false;
                    }
                }

                return true;
            }
        );

        /** Check schedules overlaps */
        foreach ($existedSchedules as $existedSchedule) {
            $scheduleStartTime = Carbon::parse($existedSchedule->start_time);
            $scheduleEndTime = Carbon::parse($existedSchedule->end_time);

            if (DateTimeHelper::timePeriodsIsOverlaps($startTime, $endTime, $scheduleStartTime, $scheduleEndTime)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if confirmed bookings in requested range
     * ($startTime - $endTime) don't overlaps with schedule period.
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $schedulableType
     * @param string $schedulableUuid
     * @param Schedule $schedule
     * @return ExecResult
     * @throws IncorrectDateRange
     */
    public function scheduleTimeIsAvailable(
        Carbon $startTime,
        Carbon $endTime,
        string $schedulableType,
        string $schedulableUuid,
        Schedule $schedule
    ): ExecResult {
        $confirmedBookings = $this->bookingRepository->getBetween(
            Carbon::parse($schedule->start_time),
            Carbon::parse($schedule->end_time),
            $schedulableType,
            $schedulableUuid,
            Booking::STATUS_CONFIRMED
        );

        foreach ($confirmedBookings as $confirmedBooking) {
            if (DateTimeHelper::timePeriodsIsOverlaps(
                Carbon::parse($confirmedBooking->start_time),
                Carbon::parse($confirmedBooking->end_time),
                $startTime,
                $endTime
            )) {
                /** Can't get price for reserved period */
                return ExecResult::instance()->setSuccess(false)->setMessage(__('errors.time_already_reserved'));
            }
        }

        return ExecResult::instance()->setSuccess();
    }

    /**
     * Get appropriate schedule for range
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $bookableType
     * @param string $bookableUuid
     * @return ExecResult
     * @throws IncorrectDateRange
     */
    public function getScheduleInRange(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        string $bookableUuid
    ): ExecResult {
        if (!in_array($bookableType, [User::class, Playground::class])) {
            return ExecResult::instance()->setSuccess(false)->setMessage(__('errors.incorrect_bookable_type'));
        }

        /**
         * Find the appropriate schedule for required dates
         * @var MergedSchedule $schedule
         */
        $schedule = null;
        $mergedSchedules = $this->scheduleRepository->getMergedSchedules(
            $bookableType,
            $bookableUuid,
            $startTime,
            $endTime
        );

        foreach ($mergedSchedules as $mergedSchedule) {
            if (Carbon::parse($mergedSchedule->start_time)->lessThanOrEqualTo($startTime) &&
                Carbon::parse($mergedSchedule->end_time)->greaterThanOrEqualTo($endTime)) {
                $schedule = $mergedSchedule;
            }
        }

        if (!$schedule) {
            return ExecResult::instance()->setSuccess(false)->setMessage(__('errors.schedule_time_unavailable'));
        }

        return ExecResult::instance()
            ->setSuccess()
            ->setData(['schedule' => $schedule]);
    }
}
