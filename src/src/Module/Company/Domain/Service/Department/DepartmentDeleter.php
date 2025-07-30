<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;

final readonly class DepartmentDeleter
{
    public function __construct(private DepartmentWriterInterface $departmentWriterRepository, private DepartmentReaderInterface $departmentReaderRepository,)
    {
    }

    public function delete(DomainEventInterface $event): void
    {
        $department = $this->departmentReaderRepository->getDepartmentByUUID($event->uuid->toString());
        $this->departmentWriterRepository->deleteDepartmentInDB($department);
    }
}
