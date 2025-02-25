<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Industry;

use App\Module\Company\Domain\Entity\Industry;

class UpdateIndustryCommand
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $name,
        private readonly ?string $description,
        private readonly Industry $industry,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
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
