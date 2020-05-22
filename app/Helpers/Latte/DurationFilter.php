<?php

namespace App\Helpers\Latte;

use App\Helpers\DurationHelper;

class DurationFilter
{

    public function __invoke(?int $seconds): string
    {
        $duration = DurationHelper::secondsToDuration($seconds);

        if ($seconds) {
            return sprintf("%02d", $duration["hours"]) . ":" . sprintf("%02d", $duration["minutes"]) . ":" . sprintf("%02d", $duration["seconds"]);
        } else {
            return "";
        }
    }

}