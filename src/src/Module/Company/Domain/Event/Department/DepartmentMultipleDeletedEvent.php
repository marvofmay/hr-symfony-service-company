<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Department;

use App\Common\Domain\Interface\DomainEventInterface;

final readonly class DepartmentMultipleDeletedEvent implements DomainEventInterface
{
    public function __construct(public array $uuids)
    {
    }
}
