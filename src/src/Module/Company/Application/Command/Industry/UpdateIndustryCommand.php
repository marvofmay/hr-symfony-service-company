<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Industry;

use App\Module\Company\Domain\Entity\Industry;

final readonly class UpdateIndustryCommand
{
    public function __construct(
        private string $name,
        private ?string $description,
        private Industry $industry,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIndustry(): Industry
    {
        return $this->industry;
    }
}
