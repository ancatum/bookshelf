<?php

namespace App\Model\Enum;

class SourceEnum
{

    const OPTIONS = [
        "my" => "Moje",
        "cbvk" => "Knihovna ČB",
        "downloaded" => "Internet",
        "other" => "Půjčeno z jiných zdrojů",
    ];


    public static function getValue($key): ?string
    {
        return self::OPTIONS[$key] ?? null;
    }

}