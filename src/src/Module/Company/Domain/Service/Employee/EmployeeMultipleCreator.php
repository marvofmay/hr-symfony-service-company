<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

readonly class EmployeeMultipleCreator
{
    public function __construct(private EmployeeWriterInterface $employeeWriterRepository, private EmployeeReaderInterface $employeeReaderRepository,)
    {
    }

    public function multipleCreate(array $data): void
    {
        $employees = new ArrayCollection();
        foreach ($data as $item) {
            $employee = new Employee();
            $employee->setFirstName($item[0]);
            $employee->setLastName($item[1]);
            if (null !== $item[2]) {
                $parentEmployee = $this->employeeReaderRepository->getEmployeeByUUID($item[2]);
                if ($parentEmployee instanceof Employee) {
                    $employee->setParentEmployee($parentEmployee);
                }
            }
            $employee->setActive((bool)$item[3]);

            $employees[] = $employee;
        }

        $this->employeeWriterRepository->saveEmployeesInDB($employees);
    }
}