<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;

readonly class EmployeeDeleter
{
    public function __construct(private EmployeeWriterInterface $employeeWriterRepository, private EmployeeReaderInterface $employeeReaderRepository)
    {
    }

    public function delete(DomainEventInterface $event): void
    {
        $employee = $this->employeeReaderRepository->getEmployeeByUUID($event->uuid->toString());
        $this->employeeWriterRepository->deleteEmployeeInDB($employee);
    }
}
