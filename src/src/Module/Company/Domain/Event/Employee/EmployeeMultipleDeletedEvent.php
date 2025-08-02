<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Employee;

use App\Common\Domain\Interface\DomainEventInterface;

final readonly class EmployeeMultipleDeletedEvent implements DomainEventInterface
{
    public function __construct(public array $uuids,) {}
}