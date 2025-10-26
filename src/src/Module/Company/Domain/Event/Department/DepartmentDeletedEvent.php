<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Department;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Department\ValueObject\DepartmentUUID;

final readonly class DepartmentDeletedEvent implements DomainEventInterface
{
    public function __construct(public DepartmentUUID $uuid)
    {
    }
}
