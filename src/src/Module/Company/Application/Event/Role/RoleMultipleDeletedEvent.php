<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;


final readonly class RoleMultipleDeletedEvent
{
    public function __construct(public array $data) {}
}