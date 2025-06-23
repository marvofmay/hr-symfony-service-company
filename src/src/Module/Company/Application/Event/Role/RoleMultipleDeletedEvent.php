<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Event\Role;

use App\Common\Domain\Interface\DomainEventInterface;
use Doctrine\Common\Collections\Collection;

final readonly class RoleMultipleDeletedEvent implements DomainEventInterface
{
    public function __construct(public Collection $roles) {}
}