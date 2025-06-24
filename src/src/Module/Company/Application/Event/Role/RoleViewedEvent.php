<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;


final readonly class RoleViewedEvent
{
    public function __construct(public string $uuid)
    {
    }
}