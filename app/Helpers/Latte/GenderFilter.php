<?php

namespace App\Helpers\Latte;

use App\Model\Enum\GenderEnum;

class GenderFilter
{

    public function __invoke(string $genderKey): string
    {
        return GenderEnum::getValue($genderKey) ?? $genderKey;
    }

}