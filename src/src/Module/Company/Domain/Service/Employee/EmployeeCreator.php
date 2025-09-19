<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\ValueObject\Address as AddressValueObject;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Entity\User;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\User\UserWriterInterface;
use App\Module\Company\Domain\Service\User\UserFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class EmployeeCreator
{
    public function __construct(
        protected EmployeeWriterInterface     $employeeWriterRepository,
        protected DepartmentReaderInterface   $departmentReaderRepository,
        protected EmployeeReaderInterface     $employeeReaderRepository,
        protected ContractTypeReaderInterface $contractTypeReaderRepository,
        protected PositionReaderInterface     $positionReaderRepository,
        protected RoleReaderInterface         $roleReaderRepository,
        protected UserWriterInterface         $userWriterRepository,
        protected UserFactory                 $userFactory,
    ) {}

    public function create(DomainEventInterface $event): void
    {
        $employee = $this->initializeEmployee($event);
        $this->employeeWriterRepository->saveEmployeeInDB($employee);
    }

    protected function initializeEmployee(DomainEventInterface $event): Employee
    {
        $employee = new Employee();
        $address = $this->createAddress($event->address);
        $contacts = $this->createContacts($event->phones, $event->emails);
        $user = $this->createUser($event->emails->toArray()[0]);

        $department = $this->departmentReaderRepository->getDepartmentByUUID($event->departmentUUID->toString());
        $role = $this->roleReaderRepository->getRoleByUUID($event->roleUUID->toString());
        $position = $this->positionReaderRepository->getPositionByUUID($event->positionUUID->toString());
        $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($event->contractTypeUUID->toString());
        $parentEmployee = $event->parentEmployeeUUID?->toString()
            ? $this->employeeReaderRepository->getEmployeeByUUID($event->parentEmployeeUUID->toString())
            : null;

        $this->setEmployeeMainData($employee, $event);
        $this->setEmployeeRelations($employee, $department, $role, $position, $contractType, $parentEmployee, $address, $contacts, $user);

        return $employee;
    }

    protected function setEmployeeMainData(Employee $employee, DomainEventInterface $event): void
    {
        $employee->setUUID($event->uuid->toString());
        $employee->setFirstName($event->firstName->getValue());
        $employee->setLastName($event->lastName->getValue());
        $employee->setPESEL($event->pesel->getValue());
        $employee->setExternalUUID($event->externalUUID);
        $employee->setInternalCode($event->internalCode);
        $employee->setEmploymentFrom(\DateTime::createFromFormat('Y-m-d', $event->employmentFrom->getValue()));
        if (null !== $event->employmentTo) {
            $employee->setEmploymentTo(\DateTime::createFromFormat('Y-m-d', $event->employmentTo->getValue()));
        }
        $employee->setActive($event->active);
    }

    protected function setEmployeeRelations(
        Employee $employee,
        Department $department,
        Role $role,
        Position $position,
        ContractType $contractType,
        ?Employee $parentEmployee,
        Address $address,
        Collection $contacts,
        User $user
    ): void {
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

    protected function createAddress(AddressValueObject $addressValueObject): Address
    {
        $address = new Address();
        $address->setStreet($addressValueObject->getStreet());
        $address->setPostcode($addressValueObject->getPostcode());
        $address->setCity($addressValueObject->getCity());
        $address->setCountry($addressValueObject->getCountry());
        $address->setActive($addressValueObject->getActive());

        return $address;
    }

    protected function createContacts(Phones $phones, Emails $emails): ArrayCollection
    {
        $contacts = new ArrayCollection();
        $dataSets = [
            ContactTypeEnum::PHONE->value => $phones->toArray(),
            ContactTypeEnum::EMAIL->value => $emails->toArray(),
        ];

        foreach ($dataSets as $type => $values) {
            foreach ($values as $value) {
                $contact = new Contact();
                $contact->setType($type);
                $contact->setData($value);
                $contacts[] = $contact;
            }
        }

        return $contacts;
    }

    protected function createUser(string $email): User
    {
        return $this->userFactory->create($email, $email);
    }
}
