<?php

namespace App\Model\Enum;

class GenderEnum
{

    const OPTIONS = [
        "man" => "Muž",
        "woman" => "Žena",
    ];


    public static function getValue($key): ?string
    {
        return self::OPTIONS[$key] ?? null;
    }

}