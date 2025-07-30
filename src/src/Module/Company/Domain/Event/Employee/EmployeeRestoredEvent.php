<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Event\Employee;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmployeeUUID;

final readonly class EmployeeRestoredEvent implements DomainEventInterface
{
    public function __construct(public EmployeeUUID $uuid,) {}
}