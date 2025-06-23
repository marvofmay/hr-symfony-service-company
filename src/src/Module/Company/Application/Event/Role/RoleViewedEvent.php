<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Common\Domain\Interface\DomainEventInterface;

final readonly class RoleViewedEvent implements DomainEventInterface
{
    public function __construct(public array $data)
    {
    }
}