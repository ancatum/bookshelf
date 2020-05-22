<?php

namespace App\Model\Enum;

class BookTypeEnum
{

    const OPTIONS = [
        "book" => "Kniha",
        "ebook" => "E-kniha",
        "audio" => "Audiokniha",
    ];


    public static function getValue($key): ?string
    {
        return self::OPTIONS[$key] ?? null;
    }

}