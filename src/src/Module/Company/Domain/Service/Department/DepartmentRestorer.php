<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;

final readonly class DepartmentRestorer
{
    public function __construct(
        private DepartmentWriterInterface $departmentWriterRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
    )
    {
    }

    public function restore(DomainEventInterface $event): void
    {
        $department = $this->departmentReaderRepository->getDeletedDepartmentByUUID($event->uuid->toString());
        $department->setDeletedAt(null);

        $address = $this->departmentReaderRepository->getDeletedAddressByDepartmentByUUID($event->uuid->toString());
        if ($address) {
            $address->setDeletedAt(null);
        }

        $contacts = $this->departmentReaderRepository->getDeletedContactsByDepartmentByUUID($event->uuid->toString());
        foreach ($contacts as $contact) {
            $contact->setDeletedAt(null);
        }

        $this->departmentWriterRepository->saveDepartmentInDB($department);
    }
}
