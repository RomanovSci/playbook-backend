<?php
declare(strict_types = 1);

namespace App\Helpers;

/**
 * Class DebugbarHelper
 * @package App\Helpers
 */
class DebugbarHelper
{
    /**
     * @return array
     */
    public static function getBaseProfilingData(): array
    {
        $profilingData = app('debugbar')->getData();

        return [
            'php_time' => $profilingData['time']['duration_str'] ?? null,
            'db_queries' => $profilingData['queries']['nb_statements'] ?? null,
            'db_time' => $profilingData['queries']['accumulated_duration_str'] ?? null,
        ];
    }
}
