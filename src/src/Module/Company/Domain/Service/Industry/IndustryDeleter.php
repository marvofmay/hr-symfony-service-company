<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;

readonly class IndustryDeleter
{
    public function __construct(private IndustryWriterInterface $industryWriterRepository)
    {
    }

    public function delete(Industry $industry): void
    {
        $this->industryWriterRepository->deleteIndustryInDB($industry);
    }
}