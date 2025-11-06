<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryUpdaterInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;

final readonly class IndustryUpdater implements IndustryUpdaterInterface
{
    public function __construct(private IndustryWriterInterface $industryWriterRepository)
    {
    }

    public function update(Industry $industry, string $name, ?string $description = null): void
    {
        $industry->setName($name);
        if (null !== $description) {
            $industry->setDescription($description);
        }

        $this->industryWriterRepository->saveIndustryInDB($industry);
    }
}
