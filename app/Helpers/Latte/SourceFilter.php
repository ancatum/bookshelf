<?php

namespace App\Helpers\Latte;

use App\Model\Enum\SourceEnum;

class SourceFilter
{

    public function __invoke(string $sourceKey): string
    {
        return SourceEnum::getValue($sourceKey) ?? $sourceKey;
    }

}