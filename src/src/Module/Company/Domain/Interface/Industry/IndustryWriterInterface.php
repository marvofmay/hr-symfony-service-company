<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Industry;

use App\Module\Company\Domain\Entity\Industry;
use Doctrine\Common\Collections\Collection;

interface IndustryWriterInterface
{
    public function saveIndustryInDB(Industry $industry): void;

    public function saveIndustriesInDB(Collection $industries): void;

    public function deleteIndustryInDB(Industry $industry): void;

    public function deleteMultipleIndustriesInDB(Collection $industries): void;
}
