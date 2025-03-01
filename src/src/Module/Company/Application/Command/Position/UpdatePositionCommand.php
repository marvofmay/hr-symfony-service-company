<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Position;

use App\Module\Company\Domain\Entity\Position;

class UpdatePositionCommand
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $name,
        private readonly ?string $description,
        private readonly ?bool $active,
        private ?array $departmentsUUID,
        private readonly Position $position,
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

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function getDepartmentsUUID(): ?array {
        return $this->departmentsUUID;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }
}
