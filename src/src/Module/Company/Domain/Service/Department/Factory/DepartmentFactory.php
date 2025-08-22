<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department\Factory;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Entity\Department;

class DepartmentFactory
{
    public function createFromEvent(DomainEventInterface $event): Department
    {
        $department = new Department();
        $department->setUUID($event->uuid->toString());

        $this->mapEventToDepartment($department, $event);

        return $department;
    }

    public function updateFromEvent(Department $department, DomainEventInterface $event): void
    {
        $this->mapEventToDepartment($department, $event);
    }

    private function mapEventToDepartment(Department $department, DomainEventInterface $event): void
    {
        $department->setUUID($event->uuid->toString());
        $department->setName($event->name->getValue());
        $department->setInternalCode($event->internalCode);
        $department->setDescription($event->description);
        $department->setActive($event->active);
    }
}