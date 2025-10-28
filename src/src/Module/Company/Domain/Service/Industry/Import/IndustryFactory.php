<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry\Import;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Enum\Industry\IndustryImportColumnEnum;

final class IndustryFactory
{
    public function createOrUpdateIndustry(array $preparedRow, array $existingIndustries): Industry
    {
        $industry = $preparedRow[IndustryImportColumnEnum::DYNAMIC_IS_INDUSTRY_WITH_NAME_ALREADY_EXISTS->value]
            ? $existingIndustries[$preparedRow[IndustryImportColumnEnum::INDUSTRY_NAME->value]]
            : new Industry();

        $industry->setName($preparedRow[IndustryImportColumnEnum::INDUSTRY_NAME->value]);
        $industry->setDescription($preparedRow[IndustryImportColumnEnum::INDUSTRY_DESCRIPTION->value]);

        return $industry;
    }
}
