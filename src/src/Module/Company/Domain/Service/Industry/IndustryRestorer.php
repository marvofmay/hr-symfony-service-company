<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;

final readonly class IndustryRestorer
{
    public function __construct(private IndustryWriterInterface $industryWriterRepository)
    {
    }

    public function restore(Industry $industry): void
    {
        $industry->setDeletedAt(null);
        $this->industryWriterRepository->saveIndustryInDB($industry);
    }
}
