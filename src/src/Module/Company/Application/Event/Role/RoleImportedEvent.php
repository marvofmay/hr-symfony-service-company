<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;


final readonly class RoleImportedEvent
{
    public function __construct(public array $data) {}
}