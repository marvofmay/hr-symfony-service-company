<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Industry;

use App\Module\Company\Domain\Entity\Industry;

interface IndustryWriterInterface
{
    public function saveIndustryInDB(Industry $industry): void;

    public function updateIndustryInDB(Industry $industry): void;

    public function saveIndustriesInDB(array $industries): void;

    public function deleteMultipleIndustriesInDB(array $selectedUUID): void;
}
