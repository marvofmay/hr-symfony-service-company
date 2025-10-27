<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Enum\Industry;

enum IndustryImportColumnEnum: int
{
    case INDUSTRY_NAME = 0;
    case INDUSTRY_DESCRIPTION = 1;
    case DYNAMIC_IS_INDUSTRY_WITH_NAME_ALREADY_EXISTS = 100;
    case DYNAMIC_INDUSTRY_UUID = 101;


    public function label(): string
    {
        return match ($this) {
            self::INDUSTRY_NAME => 'name',
            self::INDUSTRY_DESCRIPTION => 'industryName',
            self::DYNAMIC_IS_INDUSTRY_WITH_NAME_ALREADY_EXISTS => 'isIndustryWithNameAlreadyExists',
            self::DYNAMIC_INDUSTRY_UUID => 'industryUUID',
        };
    }
}
