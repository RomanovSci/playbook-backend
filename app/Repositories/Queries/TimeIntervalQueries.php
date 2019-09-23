<?php
declare(strict_types = 1);

namespace App\Repositories\Queries;

use App\Exceptions\Internal\IncorrectDateRange;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait TimeIntervalQueries
 * @package App\Repositories\Queries
 *
 * @method Builder builder()
 */
trait TimeIntervalQueries
{
    /**
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return Builder
     * @throws IncorrectDateRange
     */
    public function intersectsWith(Carbon $startTime, Carbon $endTime): Builder
    {
        if ($endTime->lessThanOrEqualTo($startTime)) {
            throw new IncorrectDateRange();
        }

        $startTime = $startTime->toDateTimeString();
        $endTime = $endTime->toDateTimeString();
        $table = $this->builder()->getModel()->getTable();

        return $this->builder()
            ->where(function (Builder $query) use ($table, $startTime, $endTime) {
                $query
                    ->where([
                        ["$table.start_time", '=', $startTime],
                        ["$table.end_time", '=', $endTime]
                    ])
                    ->orWhere([
                        ["$table.end_time", '>', $startTime],
                        ["$table.end_time", '<=', $endTime],
                    ])
                    ->orWhere([
                        ["$table.start_time", '>=', $startTime],
                        ["$table.start_time", '<', $endTime],
                    ])
                    ->orWhere([
                        ["$table.start_time", '<', $endTime],
                        ["$table.end_time", '>=', $endTime]
                    ]);
            });
    }
}
