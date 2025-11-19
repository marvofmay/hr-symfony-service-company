<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Department;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Common\Domain\Interface\NotifiableEventInterface;

final readonly class DepartmentImportedEvent implements DomainEventInterface, NotifiableEventInterface
{
    public function __construct(public array $rows, public string $importUUID)
    {
    }
}
