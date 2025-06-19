<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Position;

use App\Module\Company\Domain\Entity\Position;

final readonly class UpdatePositionCommand
{
    public function __construct(
        private string $name,
        private ?string $description,
        private ?bool $active,
        private ?array $departmentsUUID,
        private Position $position,
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
