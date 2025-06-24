<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Module\Company\Domain\Entity\Role;

final class RoleViewedEvent extends RoleEvent
{
    public function __construct(public readonly string $uuid)
    {
    }

    public function getData(): array
    {
        return [
            Role::COLUMN_UUID => $this->uuid,
        ];
    }
}