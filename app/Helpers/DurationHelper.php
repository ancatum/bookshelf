<?php

namespace App\Helpers;

class DurationHelper
{

    public static function durationToSeconds(?array $durationValues): ?int
    {
        if (!$durationValues) {
            return null;
        }
        $duration = ($durationValues[0] * 3600) + ($durationValues[1] * 60) + $durationValues[2];

        return ($duration > 0) ? $duration : null;
    }


    public static function secondsToDuration(?int $seconds): array
    {
        $hours = floor($seconds / 3600);
        $secs = $seconds % 60;
        $mins = floor(($seconds - ($hours * 3600)) / 60);

        return [
            "hours" => $hours,
            "minutes" => $mins,
            "seconds" => $secs,
        ];
    }

}