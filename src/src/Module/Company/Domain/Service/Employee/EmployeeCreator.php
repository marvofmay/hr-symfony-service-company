<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Phones;
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
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Address as AddressValueObject;

class EmployeeCreator
{
    protected ArrayCollection $contacts;

    public function __construct(
        protected Department                  $department,
        protected Employee                    $employee,
        protected ?Employee                   $parentEmployee,
        protected Role                        $role,
        protected Position                    $position,
        protected ContractType                $contractType,
        protected User                        $user,
        protected Address                     $address,
        protected EmployeeWriterInterface     $employeeWriterRepository,
        protected DepartmentReaderInterface   $departmentReaderRepository,
        protected EmployeeReaderInterface     $employeeReaderRepository,
        protected ContractTypeReaderInterface $contractTypeReaderRepository,
        protected PositionReaderInterface     $positionReaderRepository,
        protected RoleReaderInterface         $roleReaderRepository,
        protected UserWriterInterface         $userWriterRepository,
        protected UserFactory                 $userFactory,
    )
    {
        $this->contacts = new ArrayCollection();
    }

    public function create(DomainEventInterface $event): void
    {
        $this->setEmployee($event);
        $this->employeeWriterRepository->saveEmployeeInDB($this->employee);
    }

    protected function setEmployee(DomainEventInterface $event): void
    {
        $this->setDepartment($event->departmentUUID->toString());
        $this->setRole($event->roleUUID->toString());
        $this->setPosition($event->positionUUID->toString());
        $this->setContractType($event->contractTypeUUID->toString());
        $this->setParentEmployee($event->parentEmployeeUUID?->toString());
        $this->setAddress($event->address);
        $this->setContacts($event->phones, $event->emails);
        $this->setUser($event->emails->toArray()[0]);

        $this->setEmployeeMainData($event);
        $this->setEmployeeRelations();
    }

    protected function setEmployeeMainData(DomainEventInterface $event): void
    {
        $this->employee->setFirstName($event->firstName->getValue());
        $this->employee->setLastName($event->lastName->getValue());
        $this->employee->setPESEL($event->pesel->getValue());
        $this->employee->setExternalUUID($event->externalUUID);
        $this->employee->setEmploymentFrom(\DateTime::createFromFormat('Y-m-d', $event->employmentFrom->getValue()));
        if (null !== $event->employmentTo) {
            $this->employee->setEmploymentTo(\DateTime::createFromFormat('Y-m-d', $event->employmentTo->getValue()));
        }
        $this->employee->setActive($event->active);
    }

    protected function setEmployeeRelations(): void
    {
        $this->employee->setDepartment($this->department);
        $this->employee->setPosition($this->position);
        $this->employee->setContractType($this->contractType);
        $this->employee->setRole($this->role);

        $this->employee->setUser($this->user);

        foreach ($this->contacts as $contact) {
            $this->employee->addContact($contact);
        }

        if (null !== $this->parentEmployee) {
            $this->employee->setParentEmployee($this->parentEmployee);
        }

        $this->employee->setAddress($this->address);
    }

    protected function setDepartment(string $departmentUUID): void
    {
        $this->department = $this->departmentReaderRepository->getDepartmentByUUID($departmentUUID);
    }

    protected function setParentEmployee(?string $parentEmployeeUUID): void
    {
        if (null === $parentEmployeeUUID) {
            $this->parentEmployee = null;

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
        $this->contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($contractTypeUUID);
    }

    protected function setRole(string $roleUUID): void
    {
        $this->role = $this->roleReaderRepository->getRoleByUUID($roleUUID);
    }

    protected function setContacts(Phones $phones, ?Emails $emails = null): void
    {
        $dataSets = [
            ContactTypeEnum::PHONE->value   => $phones,
            ContactTypeEnum::EMAIL->value   => $emails,
        ];

        foreach ($dataSets as $type => $values) {
            foreach ($values as $value) {
                $contact = new Contact();
                $contact->setType($type);
                $contact->setData($value);

                $this->contacts[] = $contact;
            }
        }
    }

    protected function setAddress(AddressValueObject $address): void
    {
        $this->address->setStreet($address->getStreet());
        $this->address->setPostcode($address->getPostcode());
        $this->address->setCity($address->getCity());
        $this->address->setCountry($address->getCountry());
        $this->address->setActive($address->getActive());
    }

    protected function setUser(string $email): void
    {
        $user = $this->userFactory->create($email, $email);
        $this->user = $user;
    }
}
