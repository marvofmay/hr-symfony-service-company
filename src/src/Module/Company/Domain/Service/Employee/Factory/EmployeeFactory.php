<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee\Factory;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Entity\Employee;

class EmployeeFactory
{
    public function createFromEvent(DomainEventInterface $event): Employee
    {
        $employee = new Employee();
        $employee->setUUID($event->uuid->toString());

        $this->mapEventToEmployee($employee, $event);

        return $employee;
    }

    public function updateFromEvent(Employee $employee, DomainEventInterface $event): void
    {
        $this->mapEventToEmployee($employee, $event);
    }

    private function mapEventToEmployee(Employee $employee, DomainEventInterface $event): void
    {
        $employee->setUUID($event->uuid->toString());
        $employee->setFirstName($event->firstName->getValue());
        $employee->setLastName($event->lastName->getValue());
        $employee->setInternalCode($event->internalCode);
        $employee->setExternalUUID($event->externalUUID);
        $employee->setActive($event->active);
    }
}