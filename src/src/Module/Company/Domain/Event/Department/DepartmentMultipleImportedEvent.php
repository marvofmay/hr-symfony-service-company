<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Department;

use App\Common\Domain\Interface\DomainEventInterface;

final readonly class DepartmentMultipleImportedEvent implements DomainEventInterface
{
    public function __construct(public array $rows)
    {
    }
}
