<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;

readonly class IndustryService
{
    public function __construct(private IndustryWriterInterface $industryWriterRepository)
    {
    }

    public function __toString()
    {
        return 'IndustryService';
    }

    public function saveIndustryInDB(Industry $industry): void
    {
        $this->industryWriterRepository->saveIndustryInDB($industry);
    }

    public function updateIndustryInDB(Industry $industry): void
    {
        $this->industryWriterRepository->updateIndustryInDB($industry);
    }

    public function saveIndustriesInDB(array $industries): void
    {
        $this->industryWriterRepository->saveIndustriesInDB($industries);
    }

    public function deleteMultipleIndustriesInDB(array $selectedUUID): void
    {
        $this->industryWriterRepository->deleteMultipleIndustriesInDB($selectedUUID);
    }
}
