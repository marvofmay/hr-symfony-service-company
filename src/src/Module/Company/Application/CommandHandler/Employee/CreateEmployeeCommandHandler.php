<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Employee;

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
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Service\Employee\EmployeeService;

readonly class CreateEmployeeCommandHandler
{
    private CreateEmployeeCommand $command;
    private Company $company;
    private Department $department;

    public function __construct(
        private EmployeeService $employeeService,
        private CompanyReaderInterface $companyReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private PositionReaderInterface $positionReaderRepository,
        private ContractTypeReaderInterface $contractTypeReaderRepository,
        private RoleReaderInterface $roleReaderRepository,
        private EmployeeReaderInterface $employeeReaderRepository,
        private User $user,
        private Employee $employee
    )
    {
    }

    public function __invoke(CreateEmployeeCommand $command): void
    {
        $this->command = $command;

        $this->company = $this->getCompany();
        $this->department = $this->getDepartment();

        $this->setEmployeeMainData();
        $this->setEmployeeCompanyData();
        $this->setEmployeeRelations();

        $this->employeeService->saveEmployeeInDB($this->employee);
    }

    private function setEmployeeMainData(): void
    {
        $this->employee->setFirstName($this->command->firstName);
        $this->employee->setLastName($this->command->lastName);
        $this->employee->setPESEL($this->command->pesel);
        $this->employee->setEmploymentFrom(\DateTime::createFromFormat('Y-m-d', $this->command->employmentFrom));
        if (null !== $this->command->employmentTo) {
            $this->employee->setEmploymentTo(\DateTime::createFromFormat('Y-m-d', $this->command->employmentTo));
        }
    }

    private function setEmployeeCompanyData (): void
    {
        $this->employee->setCompany($this->company);
        $this->employee->setDepartment($this->department);
        $this->employee->setPosition($this->getPosition());
        $this->employee->setContractType($this->getContractType());
        $this->employee->setRole($this->getRole());
        $this->employee->setActive($this->command->active);
    }

    private function setEmployeeRelations(): void
    {
        $this->employee->setUser($this->getUser());

        foreach ($this->command->phones as $phone) {
            $this->employee->addContact($this->getContacts($this->company, $this->department, $phone));
        }

        if (!empty($this->command->parentEmployeeUUID)) {
            $this->employee->setParentEmployee($this->getEmployee());
        }

        $this->employee->setAddress($this->getAddress($this->company, $this->department));
    }

    private function getCompany(): Company
    {
        return $this->companyReaderRepository->getCompanyByUUID($this->command->companyUUID);
    }

    private function getDepartment(): Department
    {
        return $this->departmentReaderRepository->getDepartmentByUUID($this->command->departmentUUID);
    }

    private function getEmployee(): Employee
    {
        return $this->employeeReaderRepository->getEmployeeByUUID($this->command->parentEmployeeUUID);
    }

    private function getPosition(): Position
    {
        return $this->positionReaderRepository->getPositionByUUID($this->command->positionUUID);
    }

    private function getContractType(): ContractTYpe
    {
        return $this->contractTypeReaderRepository->getContractTypeByUUID($this->command->contractTypeUUID);
    }

    private function getRole(): Role
    {
        return $this->roleReaderRepository->getRoleByUUID($this->command->roleUUID);
    }

    private function getContacts(Company $company, Department $department, string $phone): Contact
    {
        $contact = new Contact();
        $contact->setCompany($company);
        $contact->setDepartment($department);
        $contact->setType(ContactTypeEnum::PHONE->value);
        $contact->setData($phone);

        return $contact;
    }

    private function getAddress(Company $company, Department $department): Address
    {
        $addressObject = $this->command->address;

        $address = new Address();
        $address->setCompany($company);
        $address->setDepartment($department);
        $address->setStreet($addressObject->street);
        $address->setPostcode($addressObject->postcode);
        $address->setCity($addressObject->city);
        $address->setCountry($addressObject->country);

        return $address;
    }

    private function getUser(): User
    {
        $this->user->setEmail($this->command->email);
        $this->user->setPassword(sprintf('%s-%s', $this->command->email, $this->command->firstName));

        return $this->user;
    }
}
