<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryDeleterInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;

final readonly class IndustryDeleter implements IndustryDeleterInterface
{
    public function __construct(private IndustryWriterInterface $industryWriterRepository)
    {
    }

    public function delete(Industry $industry): void
    {
        $this->industryWriterRepository->deleteIndustryInDB($industry);
    }
}
