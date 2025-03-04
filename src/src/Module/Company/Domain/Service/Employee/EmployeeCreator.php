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
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Doctrine\Common\Collections\ArrayCollection;

class EmployeeCreator
{
    private ArrayCollection $contacts;

    public function __construct(
        private Company $company,
        private Department $department,
        private readonly Employee $employee,
        private Employee $parentEmployee,
        private Role $role,
        private Position $position,
        private ContractType $contractType,
        private readonly User $user,
        private readonly Address $address,
        private readonly EmployeeWriterInterface $employeeWriterRepository,
        private readonly CompanyReaderInterface $companyReaderRepository,
        private readonly DepartmentReaderInterface $departmentReaderRepository,
        private readonly EmployeeReaderInterface $employeeReaderRepository,
        private readonly ContractTypeReaderInterface $contractTypeReaderRepository,
        private readonly PositionReaderInterface $positionReaderRepository,
        private readonly RoleReaderInterface $roleReaderRepository,
    ){
        $this->contacts = new ArrayCollection();
    }

    public function create(CreateEmployeeCommand $command): void
    {
        $this->setCompany($command->companyUUID);
        $this->setDepartment($command->departmentUUID);
        $this->setRole($command->roleUUID);
        $this->setPosition($command->positionUUID);
        $this->setContractType($command->contractTypeUUID);
        $this->setParentEmployee($command->parentEmployeeUUID);
        $this->setUser($command->email, $command->firstName);
        $this->setAddress($command->address);
        $this->setContacts($command->phones);

        $this->setEmployeeMainData($command);
        $this->setEmployeeCompanyData();
        $this->setEmployeeRelations();

        $this->employeeWriterRepository->saveEmployeeInDB($this->employee);
    }

    private function setEmployeeMainData(CreateEmployeeCommand $command): void
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

    private function setEmployeeCompanyData (): void
    {
        $this->employee->setCompany($this->company);
        $this->employee->setDepartment($this->department);
        $this->employee->setPosition($this->position);
        $this->employee->setContractType($this->contractType);
        $this->employee->setRole($this->role);
    }

    private function setEmployeeRelations(): void
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
    private function setCompany(string $companyUUID): void
    {
        $this->company = $this->companyReaderRepository->getCompanyByUUID($companyUUID);
    }

    private function setDepartment(string $departmentUUID): void
    {
        $this->department = $this->departmentReaderRepository->getDepartmentByUUID($departmentUUID);
    }

    private function setParentEmployee(?string $parentEmployeeUUID): void
    {
        if (null === $parentEmployeeUUID) {
            return;
        }

        $this->parentEmployee = $this->employeeReaderRepository->getEmployeeByUUID($parentEmployeeUUID);
    }

    private function setPosition(string $positionUUID): void
    {
        $this->position = $this->positionReaderRepository->getPositionByUUID($positionUUID);
    }

    private function setContractType(string $contractTypeUUID): void
    {
        $this->contractType =  $this->contractTypeReaderRepository->getContractTypeByUUID($contractTypeUUID);
    }

    private function setRole(string $roleUUID): void
    {
        $this->role = $this->roleReaderRepository->getRoleByUUID($roleUUID);
    }

    private function setContacts(array $phones): void
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

    private function setAddress(AddressDTO $addressDTO): void
    {
        $this->address->setCompany($this->company);
        $this->address->setDepartment($this->department);
        $this->address->setStreet($addressDTO->street);
        $this->address->setPostcode($addressDTO->postcode);
        $this->address->setCity($addressDTO->city);
        $this->address->setCountry($addressDTO->country);
    }

    private function setUser(string $email, string $firstName): void
    {
        $this->user->setEmail($email);
        $this->user->setPassword(sprintf('%s-%s', $email, $firstName));
    }
}