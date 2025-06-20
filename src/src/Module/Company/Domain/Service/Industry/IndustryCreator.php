<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;

readonly class IndustryCreator
{
    public function __construct(private IndustryWriterInterface $industryWriterRepository)
    {
    }

    public function create(string $name, ?string $description): void
    {
        $industry = new Industry();
        $industry->setName($name);
        $industry->setDescription($description);

        $this->industryWriterRepository->saveIndustryInDB($industry);
    }
}
