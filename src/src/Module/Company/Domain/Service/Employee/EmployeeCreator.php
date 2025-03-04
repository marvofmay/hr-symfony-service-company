<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\DTO\AddressDTO;
use App\Module\Company\Application\Command\Employee\CreateEmployeeCommand;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Entity\User;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Address\AddressWriterInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Contact\ContactWriterInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\User\UserWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

class EmployeeCreator
{
    protected ArrayCollection $contacts;

    public function __construct(
        protected Company $company,
        protected Department $department,
        protected Employee $employee,
        protected Employee $parentEmployee,
        protected Role $role,
        protected Position $position,
        protected ContractType $contractType,
        protected readonly User $user,
        protected readonly Address $address,
        protected readonly EmployeeWriterInterface $employeeWriterRepository,
        protected readonly CompanyReaderInterface $companyReaderRepository,
        protected readonly DepartmentReaderInterface $departmentReaderRepository,
        protected readonly EmployeeReaderInterface $employeeReaderRepository,
        protected readonly ContractTypeReaderInterface $contractTypeReaderRepository,
        protected readonly PositionReaderInterface $positionReaderRepository,
        protected readonly RoleReaderInterface $roleReaderRepository,
        protected readonly ContactWriterInterface $contactWriterRepository,
        protected readonly AddressWriterInterface $addressWriterRepository,
        protected readonly UserWriterInterface $userWriterRepository,
    ) {
        $this->contacts = new ArrayCollection();
    }

    public function create(CreateEmployeeCommand $command): void
    {
        $this->setEmployee($command);
        $this->employeeWriterRepository->saveEmployeeInDB($this->employee);
    }

    protected function setEmployee($command): void
    {
        $this->setCompany($command->companyUUID);
        $this->setDepartment($command->departmentUUID);
        $this->setRole($command->roleUUID);
        $this->setPosition($command->positionUUID);
        $this->setContractType($command->contractTypeUUID);
        $this->setParentEmployee($command->parentEmployeeUUID);
        $this->setAddress($command->address);
        $this->setContacts($command->phones);
        $this->setUser($command->email, $command->firstName);

        $this->setEmployeeMainData($command);
        $this->setEmployeeCompanyData();
        $this->setEmployeeRelations();
    }

    protected function setEmployeeMainData($command): void
    {
        $this->employee->setFirstName($command->firstName);
        $this->employee->setLastName($command->lastName);
        $this->employee->setPESEL($command->pesel);
        $this->employee->setExternalUUID($command->externalUUID);
        $this->employee->setEmploymentFrom(\DateTime::createFromFormat('Y-m-d', $command->employmentFrom));
        if (null !== $command->employmentTo) {
            $this->employee->setEmploymentTo(\DateTime::createFromFormat('Y-m-d', $command->employmentTo));
        }
        $this->employee->setActive($command->active);
    }

    protected function setEmployeeCompanyData (): void
    {
        $this->employee->setCompany($this->company);
        $this->employee->setDepartment($this->department);
        $this->employee->setPosition($this->position);
        $this->employee->setContractType($this->contractType);
        $this->employee->setRole($this->role);
    }

    protected function setEmployeeRelations(): void
    {
        $this->employee->setUser($this->user);

        foreach ($this->contacts as $contact) {
            $this->employee->addContact($contact);
        }

        if (null !== $this->parentEmployee) {
            $this->employee->setParentEmployee($this->parentEmployee);
        }

        $this->employee->setAddress($this->address);
    }
    protected function setCompany(string $companyUUID): void
    {
        $this->company = $this->companyReaderRepository->getCompanyByUUID($companyUUID);
    }

    protected function setDepartment(string $departmentUUID): void
    {
        $this->department = $this->departmentReaderRepository->getDepartmentByUUID($departmentUUID);
    }

    protected function setParentEmployee(?string $parentEmployeeUUID): void
    {
        if (null === $parentEmployeeUUID) {
            return;
        }

        $this->parentEmployee = $this->employeeReaderRepository->getEmployeeByUUID($parentEmployeeUUID);
    }

    protected function setPosition(string $positionUUID): void
    {
        $this->position = $this->positionReaderRepository->getPositionByUUID($positionUUID);
    }

    protected function setContractType(string $contractTypeUUID): void
    {
        $this->contractType =  $this->contractTypeReaderRepository->getContractTypeByUUID($contractTypeUUID);
    }

    protected function setRole(string $roleUUID): void
    {
        $this->role = $this->roleReaderRepository->getRoleByUUID($roleUUID);
    }

    protected function setContacts(array $phones): void
    {
        foreach ($phones as $phone) {
            $contact = new Contact();
            $contact->setCompany($this->company);
            $contact->setDepartment($this->department);
            $contact->setType(ContactTypeEnum::PHONE->value);
            $contact->setData($phone);

            $this->contacts[] = $contact;
        }
    }

    protected function setAddress(AddressDTO $addressDTO): void
    {
        $this->address->setCompany($this->company);
        $this->address->setDepartment($this->department);
        $this->address->setStreet($addressDTO->street);
        $this->address->setPostcode($addressDTO->postcode);
        $this->address->setCity($addressDTO->city);
        $this->address->setCountry($addressDTO->country);
    }

    protected function setUser(string $email, string $firstName): void
    {
        $password = sprintf('%s-%s', $email, $firstName);
        if (null === $this->employee->getUser()) {
           $this->user->setEmail($email);
           $this->user->setPassword($password);
        }
    }
}