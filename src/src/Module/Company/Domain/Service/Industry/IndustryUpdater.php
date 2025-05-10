<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;

readonly class IndustryUpdater
{
    public function __construct(private IndustryWriterInterface $industryWriterRepository)
    {
    }

    public function update(Industry $industry, string $name, ?string $description): void
    {
        $industry->setName($name);
        $industry->setDescription($description);
        $industry->setUpdatedAt(new \DateTime());

        $this->industryWriterRepository->saveIndustryInDB($industry);
    }
}