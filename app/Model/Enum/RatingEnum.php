<?php

namespace App\Model\Enum;

class RatingEnum
{

    const OPTIONS = [
        0 => "nečitelné",
        1 => "1 hvězdička",
        2 => "2 hvězdičky",
        3 => "3 hvězdičky",
        4 => "4 hvězdičky",
        5 => "5 hvězdiček",
    ];

    public static function getValue(int $ratingValue): ?string
    {
        return self::OPTIONS[$ratingValue] ?? null;
    }

}