<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryCreatorInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;

final readonly class IndustryCreator implements IndustryCreatorInterface
{
    public function __construct(private IndustryWriterInterface $industryWriterRepository)
    {
    }

    public function create(string $name, ?string $description = null): void
    {
        $industry = Industry::create(trim($name), $description);

        $this->industryWriterRepository->saveIndustryInDB($industry);
    }
}
