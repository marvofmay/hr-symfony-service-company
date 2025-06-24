<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

final class RoleAssignedAccessesEvent extends RoleEvent
{
    public function __construct(public readonly array $data,) {}

    public function getData(): array
    {
        return $this->data;
    }
}