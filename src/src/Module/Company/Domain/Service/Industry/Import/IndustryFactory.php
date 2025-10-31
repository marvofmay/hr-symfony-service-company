<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry\Import;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Enum\Industry\IndustryImportColumnEnum;

final class IndustryFactory
{
    public function create(array $industryData): Industry
    {
        $industry = new Industry();
        $this->fillData($industry, $industryData);

        return $industry;
    }

    public function update(array $industryData, array $existingIndustries): Industry
    {
        $industry = $existingIndustries[$industryData[IndustryImportColumnEnum::INDUSTRY_NAME->value]];
        $this->fillData($industry, $industryData);

        return $industry;
    }

    private function fillData(Industry $industry, array $industryData): void
    {
        $name = $industryData[IndustryImportColumnEnum::INDUSTRY_NAME->value] ?? null;
        $description = $industryData[IndustryImportColumnEnum::INDUSTRY_DESCRIPTION->value] ?? null;

        $industry->setName($name);
        $industry->setDescription($description);
    }
}
