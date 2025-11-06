<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry\Import;

use App\Module\Company\Domain\Enum\Industry\IndustryImportColumnEnum;
use App\Module\Company\Domain\Interface\Industry\Importer\IndustriesImporterInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class IndustriesImporter implements IndustriesImporterInterface
{
    private array $industries = [];

    public function __construct(
        private readonly IndustryWriterInterface $industryWriterRepository,
        private readonly IndustryFactory $industryFactory,
    ) {
    }

    public function save(array $preparedRows, array $existingIndustries): void
    {
        foreach ($preparedRows as $preparedRow) {
            if (array_key_exists($preparedRow[IndustryImportColumnEnum::INDUSTRY_NAME->value], $existingIndustries)) {
                $industryToUpdate = $existingIndustries[$preparedRow[IndustryImportColumnEnum::INDUSTRY_NAME->value]];
                $industry = $this->industryFactory->update(industry: $industryToUpdate, data: $preparedRow);
            } else {
                $industry = $this->industryFactory->create(data: $preparedRow);
            }
            $this->industries[] = $industry;
        }

        $this->industryWriterRepository->saveIndustriesInDB(new ArrayCollection($this->industries));
    }
}
