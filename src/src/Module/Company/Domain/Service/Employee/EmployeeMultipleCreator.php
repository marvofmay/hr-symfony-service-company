<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Address\AddressWriterInterface;
use App\Module\Company\Domain\Interface\Contact\ContactWriterInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class EmployeeMultipleCreator
{
    private Employee $employee;

    public function __construct(
        private DepartmentReaderInterface   $departmentReaderRepository,
        private EmployeeReaderInterface     $employeeReaderRepository,
        private PositionReaderInterface     $positionReaderRepository,
        private ContractTypeReaderInterface $contractTypeReaderRepository,
        private RoleReaderInterface         $roleReaderRepository,
        private EmployeeWriterInterface     $employeeWriterRepository,
        private AddressWriterInterface      $addressWriterRepository,
        private ContactWriterInterface      $contactWriterRepository,
    )
    {
    }

    public function multipleCreate(array $data): void
    {
        $this->setEmployees($data);
    }

    private function setEmployees(array $data): void
    {
        $employees = new ArrayCollection();
        $temporaryEmployeeMap = [];

        foreach ($data as $item) {
            $this->setEmployee($item);
            $this->setMainEmployeeData($item);
            $this->setAddress($item);
            $this->setContacts($item);

            if (is_int($item[ImportEmployeesFromXLSX::COLUMN_EMPLOYEE_UUID])) {
                $temporaryEmployeeMap[$item[ImportEmployeesFromXLSX::COLUMN_EMPLOYEE_UUID]] = $this->employee;
            }

            $employees[] = $this->employee;
        }

        foreach ($data as $index => $item) {
            $employee = $employees[$index];
            $this->employee = $employee;
            $this->setRelations($item, $temporaryEmployeeMap);
        }

        $this->employeeWriterRepository->saveEmployeesInDB($employees);
    }

    private function setEmployee(array $item): void
    {
        if (null === $item[ImportEmployeesFromXLSX::COLUMN_EMPLOYEE_UUID] || is_int($item[ImportEmployeesFromXLSX::COLUMN_EMPLOYEE_UUID])) {
            $this->employee = new Employee();
        } else if (is_string($item[ImportEmployeesFromXLSX::COLUMN_EMPLOYEE_UUID])) {
            $employee = $this->employeeReaderRepository->getEmployeeByUUID($item[ImportEmployeesFromXLSX::COLUMN_EMPLOYEE_UUID]);
            if ($employee === null) {
                $this->employee = new Employee();
            } else {
                $this->employee = $employee;
            }
        }
    }

    private function setMainEmployeeData(array $item): void
    {
        $this->employee->setFirstName($item[ImportEmployeesFromXLSX::COLUMN_FIRST_NAME]);
        $this->employee->setLastName($item[ImportEmployeesFromXLSX::COLUMN_LAST_NAME]);
        $this->employee->setPESEL((string)$item[ImportEmployeesFromXLSX::COLUMN_PESEL]);

        $employmentFrom = \DateTime::createFromFormat('d-m-Y', $item[ImportEmployeesFromXLSX::COLUMN_EMPLOYMENT_FROM]);
        $employmentTo = null !== $item[ImportEmployeesFromXLSX::COLUMN_EMPLOYMENT_TO] ? \DateTime::createFromFormat('d-m-Y', $item[ImportEmployeesFromXLSX::COLUMN_EMPLOYMENT_TO]) : null;

        $this->employee->setEmploymentFrom($employmentFrom);
        $this->employee->setEmploymentTo($employmentTo);
        $this->employee->setActive((bool)$item[ImportEmployeesFromXLSX::COLUMN_ACTIVE]);
    }

    private function setAddress(array $item): void
    {
        if (null !== $item[ImportEmployeesFromXLSX::COLUMN_EMPLOYEE_UUID] && is_string($item[ImportEmployeesFromXLSX::COLUMN_EMPLOYEE_UUID])) {
            $address = $this->employee->getAddress();
            $this->addressWriterRepository->deleteAddressInDB($address, Address::HARD_DELETED_AT);
        }

        $address = new Address();
        $address->setStreet($item[ImportEmployeesFromXLSX::COLUMN_STREET]);
        $address->setPostcode($item[ImportEmployeesFromXLSX::COLUMN_POSTCODE]);
        $address->setCity($item[ImportEmployeesFromXLSX::COLUMN_CITY]);
        $address->setCountry($item[ImportEmployeesFromXLSX::COLUMN_COUNTRY]);

        $this->employee->setAddress($address);
    }

    private function setContacts(array $item): void
    {
        if (null !== $item[ImportEmployeesFromXLSX::COLUMN_EMPLOYEE_UUID]) {
            foreach ([ContactTypeEnum::PHONE, ContactTypeEnum::EMAIL,] as $enum) {
                $contacts = $this->employee->getContacts($enum);
                $this->contactWriterRepository->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);
            }
        }

        if (null !== $item[ImportEmployeesFromXLSX::COLUMN_PHONE]) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::PHONE->value);
            $contact->setData($item[ImportEmployeesFromXLSX::COLUMN_PHONE]);
            $contact->setActive(true);
            $this->employee->addContact($contact);
        }

        if (null !== $item[ImportEmployeesFromXLSX::COLUMN_EMAIL]) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::EMAIL->value);
            $contact->setData($item[ImportEmployeesFromXLSX::COLUMN_EMAIL]);
            $contact->setActive(true);
            $this->employee->addContact($contact);
        }
    }

    private function setRelations(array $item, array $temporaryEmployeeMap): void
    {
        $this->setDepartment($item);
        $this->setPosition($item);
        $this->setContractType($item);
        $this->setRole($item);
        $this->setParentEmployee($item, $temporaryEmployeeMap);
    }

    private function setDepartment(array $item): void
    {
        $department = $this->departmentReaderRepository->getDepartmentByUUID($item[ImportEmployeesFromXLSX::COLUMN_DEPARTMENT_UUID]);
        if ($department instanceof Department) {
            $this->employee->setDepartment($department);
        }
    }

    private function setPosition(array $item): void
    {
        $position = $this->positionReaderRepository->getPositionByUUID($item[ImportEmployeesFromXLSX::COLUMN_POSITION_UUID]);
        if ($position instanceof Position) {
            $this->employee->setPosition($position);
        }
    }

    private function setContractType(array $item): void
    {
        $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($item[ImportEmployeesFromXLSX::COLUMN_CONTACT_TYPE_UUID]);
        if ($contractType instanceof ContractType) {
            $this->employee->setContractType($contractType);
        }
    }

    private function setRole(array $item): void
    {
        $role = $this->roleReaderRepository->getRoleByUUID($item[ImportEmployeesFromXLSX::COLUMN_ROLE_UUID]);
        if ($role instanceof Role) {
            $this->employee->setRole($role);
        }
    }

    private function setParentEmployee(array $item, array $temporaryEmployeeMap): void {
        $parentEmployeeUUID = $item[ImportEmployeesFromXLSX::COLUMN_PARENT_EMPLOYEE_UUID] ?? null;
        if ($parentEmployeeUUID !== null) {
            if (is_int($parentEmployeeUUID) && isset($temporaryEmployeeMap[$parentEmployeeUUID])) {
                $this->employee->setParentEmployee($temporaryEmployeeMap[$parentEmployeeUUID]);
            } else if (is_string($parentEmployeeUUID)) {
                $parentEmployee = $this->employeeReaderRepository->getEmployeeByUUID($parentEmployeeUUID);
                if ($parentEmployee instanceof Employee) {
                    $this->employee->setParentEmployee($parentEmployee);
                }
            }
        }
    }
}