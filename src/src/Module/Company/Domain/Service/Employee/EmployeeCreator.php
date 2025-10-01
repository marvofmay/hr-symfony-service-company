<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Entity\User;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Service\Employee\Factory\EmployeeFactory;
use App\Module\Company\Domain\Service\Factory\AddressFactory;
use App\Module\Company\Domain\Service\Factory\ContactFactory;
use App\Module\Company\Domain\Service\User\UserFactory;

readonly class EmployeeCreator
{
    public function __construct(
        private EmployeeFactory             $employeeFactory,
        private AddressFactory              $addressFactory,
        private ContactFactory              $contactFactory,
        private EmployeeWriterInterface     $employeeWriterRepository,
        private DepartmentReaderInterface   $departmentReaderRepository,
        private EmployeeReaderInterface     $employeeReaderRepository,
        private ContractTypeReaderInterface $contractTypeReaderRepository,
        private PositionReaderInterface     $positionReaderRepository,
        private RoleReaderInterface         $roleReaderRepository,
        private UserFactory                 $userFactory,
    )
    {
    }

    public function create(DomainEventInterface $event): void
    {
        $employee = $this->employeeFactory->create($event);

        $email = $event->emails->toArray()[0];
        $user = $this->userFactory->create($email, $email);

        $department = $this->departmentReaderRepository->getDepartmentByUUID($event->departmentUUID->toString());
        $role = $this->roleReaderRepository->getRoleByUUID($event->roleUUID->toString());
        $position = $this->positionReaderRepository->getPositionByUUID($event->positionUUID->toString());
        $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($event->contractTypeUUID->toString());
        $parentEmployee = $event->parentEmployeeUUID?->toString()
            ? $this->employeeReaderRepository->getEmployeeByUUID($event->parentEmployeeUUID->toString())
            : null;

        $address = $this->addressFactory->create($event->address);
        $contacts = $this->contactFactory->create($event->phones, $event->emails);

        $this->setEmployeeRelations($employee, $department, $role, $position, $contractType, $parentEmployee, $address, $contacts, $user);

        $this->employeeWriterRepository->saveEmployeeInDB($employee);
    }

    protected function setEmployeeRelations(
        Employee     $employee,
        Department   $department,
        Role         $role,
        Position     $position,
        ContractType $contractType,
        ?Employee    $parentEmployee,
        Address      $address,
        array        $contacts,
        User         $user
    ): void
    {
        $employee->setDepartment($department);
        $employee->setRole($role);
        $employee->setPosition($position);
        $employee->setContractType($contractType);
        $employee->setParentEmployee($parentEmployee);
        $employee->setAddress($address);
        $employee->setUser($user);

        foreach ($contacts as $contact) {
            $employee->addContact($contact);
        }
    }
}
