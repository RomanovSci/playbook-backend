<?php

namespace App\Services;

use App\Models\Playground;
use App\Models\User;
use App\Repositories\ScheduleRepository;
use Carbon\Carbon;

/**
 * Class BookingAvailabilityChecker
 * @package App\Services\Booking
 */
class BookingService
{
    /**
     * Determinate if booking can create
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param User $creator
     * @param string $bookableType
     * @param int $bookableId
     * @return array
     */
    public function canCreate(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        int $bookableId,
        User $creator
    ): array {
        $result = [
            'success' => false,
            'message' => '',
        ];

        /** Can't book unbookable entities */
        if (!in_array($bookableType, [User::class, Playground::class])) {
            $result['message'] = 'Incorrect bookable type';
            return $result;
        }

        /** Can't create booking for myself */
        if ($bookableType === User::class && $creator->id === $bookableId) {
            $result['message'] = 'Can\'t create booking for myself';
            return $result;
        }

        $properSchedule = null;
        $mergedSchedules = ScheduleRepository::getMergedSchedules($bookableType, $bookableId);

        foreach ($mergedSchedules as $mergedSchedule) {
            if (Carbon::parse($mergedSchedule->start_time)->lessThanOrEqualTo($startTime) &&
                Carbon::parse($mergedSchedule->end_time)->greaterThanOrEqualTo($endTime)) {
                $properSchedule = $mergedSchedule;
            }
        }

        /** Can't create booking for not existed schedules */
        if (!$properSchedule) {
            $result['message'] = 'Schedules for this time interval doesn\'t exists';
            return $result;
        }

        $result['success'] = true;
        return $result;
    }
}
