<?php
declare(strict_types = 1);

namespace App\Services\Booking;

use App\Exceptions\Internal\IncorrectDateRange;
use App\Helpers\DateTimeHelper;
use App\Helpers\MoneyHelper;
use App\Models\MergedSchedule;
use App\Services\ExecResult;
use App\Services\Schedule\ScheduleTimingService;
use Carbon\Carbon;

/**
 * Class BookingPricingService
 * @package App\Services\Booking
 */
class BookingPricingService
{
    /**
     * @var ScheduleTimingService
     */
    protected $scheduleTimingService;

    /**
     * BookingPricingService constructor.
     * @param ScheduleTimingService $scheduleTimingService
     */
    public function __construct(ScheduleTimingService $scheduleTimingService)
    {
        $this->scheduleTimingService = $scheduleTimingService;
    }

    /**
     * Get booking price for period ($startTime - $endTime)
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $bookableType
     * @param string $bookableUuid
     * @return ExecResult
     *
     * @throws IncorrectDateRange
     */
    public function getBookingPrice(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        string $bookableUuid
    ): ExecResult {
        $getScheduleResult = $this->scheduleTimingService->getScheduleInRange(
            $startTime,
            $endTime,
            $bookableType,
            $bookableUuid
        );

        if (!$getScheduleResult->getSuccess()) {
            return $getScheduleResult;
        }

        /** @var MergedSchedule $appropriateSchedule */
        $appropriateSchedule = $getScheduleResult->getData('schedule');
        $scheduleAvailabilityCheckResult = $this->scheduleTimingService->scheduleTimeIsAvailable(
            $startTime,
            $endTime,
            $bookableType,
            $bookableUuid,
            $appropriateSchedule
        );

        if (!$scheduleAvailabilityCheckResult->getSuccess()) {
            return $scheduleAvailabilityCheckResult;
        }

        $price = 0;
        $currencySubunit = currency($appropriateSchedule->currency)->getSubunit();

        foreach ($appropriateSchedule->getSchedules() as $schedule) {
            $minutesRate = MoneyHelper::getMinutesRate($schedule);
            $overlappedMinutes = DateTimeHelper::getOverlappedMinutesAmount(
                $startTime,
                $endTime,
                Carbon::parse($schedule->start_time),
                Carbon::parse($schedule->end_time)
            );
            $price += round(
                    (money($minutesRate, $appropriateSchedule->currency)
                        ->multiply($overlappedMinutes)
                        ->getAmount()) / $currencySubunit
                ) * $currencySubunit;
        }

        return ExecResult::instance()
            ->setSuccess()
            ->setData([
                'currency' => $appropriateSchedule->currency,
                'price' => $price,
            ]);
    }
}
