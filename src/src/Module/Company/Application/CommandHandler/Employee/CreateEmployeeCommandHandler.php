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

    public function __construct(
        private EmployeeService $employeeService,
        private CompanyReaderInterface $companyReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private PositionReaderInterface $positionReaderRepository,
        private ContractTypeReaderInterface $contractTypeReaderRepository,
        private RoleReaderInterface $roleReaderRepository,
        private EmployeeReaderInterface $employeeReaderRepository
    )
    {
    }

    public function __invoke(CreateEmployeeCommand $command): void
    {
        $this->command = $command;

        $company = $this->getCompany();
        $department = $this->getDepartment();

        $employee = new Employee();
        $employee->setFirstName($this->command->firstName);
        $employee->setLastName($this->command->lastName);
        $employee->setPESEL($this->command->pesel);
        $employee->setEmploymentFrom(\DateTime::createFromFormat('Y-m-d', $this->command->employmentFrom));
        $employee->setActive($this->command->active);
        $employee->setCompany($company);
        $employee->setDepartment($department);
        $employee->setPosition($this->getPosition());
        $employee->setContractType($this->getContractType());
        $employee->setRole($this->getRole());
        $employee->setAddress($this->getAddress($company, $department));

        foreach ($this->command->phones as $phone) {
            $employee->addContact($this->getContacts($company, $department, $phone));
        }

        if (null !== $this->command->employmentTo) {
            $employee->setEmploymentTo(\DateTime::createFromFormat('Y-m-d', $this->command->employmentTo));
        }

        if (!empty($this->command->parentEmployeeUUID)) {
            $employee->setParentEmployee($this->getEmployee());
        }

        $this->employeeService->saveEmployeeInDB($employee);
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
}
