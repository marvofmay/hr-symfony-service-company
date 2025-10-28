<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry\Import;

use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class IndustriesImporter
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
            $industry = $this->industryFactory->createOrUpdateIndustry(preparedRow: $preparedRow, existingIndustries: $existingIndustries);
            $this->industries[] = $industry;
        }

        $this->industryWriterRepository->saveIndustriesInDB(new ArrayCollection($this->industries));
    }
}
