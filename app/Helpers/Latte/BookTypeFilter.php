<?php

namespace App\Helpers\Latte;

use App\Model\Enum\BookTypeEnum;

class BookTypeFilter
{

    public function __invoke(string $bookTypeKey): string
    {
        return BookTypeEnum::getValue($bookTypeKey) ?? $bookTypeKey;
    }

}