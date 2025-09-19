<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\ValueObject\Address as AddressValueObject;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\User;
use App\Module\Company\Domain\Interface\Address\AddressWriterInterface;
use App\Module\Company\Domain\Interface\Contact\ContactWriterInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeWriterInterface;
use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use App\Module\Company\Domain\Interface\User\UserWriterInterface;
use App\Module\Company\Domain\Service\User\UserFactory;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class EmployeeUpdater extends EmployeeCreator
{
    public function __construct(
        protected EmployeeWriterInterface     $employeeWriterRepository,
        protected EmployeeReaderInterface     $employeeReaderRepository,
        protected ContactWriterInterface      $contactWriterRepository,
        protected AddressWriterInterface      $addressWriterRepository,
        protected UserWriterInterface         $userWriterRepository,
        protected UserPasswordHasherInterface $userPasswordHasher,
        protected UserFactory                 $userFactory,
        protected DepartmentReaderInterface   $departmentReaderRepository,
        protected ContractTypeReaderInterface $contractTypeReaderRepository,
        protected PositionReaderInterface     $positionReaderRepository,
        protected RoleReaderInterface         $roleReaderRepository,
    ) {
        parent::__construct(
            $employeeWriterRepository,
            $departmentReaderRepository,
            $employeeReaderRepository,
            $contractTypeReaderRepository,
            $positionReaderRepository,
            $roleReaderRepository,
            $userWriterRepository,
            $userFactory
        );
    }

    public function update(DomainEventInterface $event): void
    {
        $employee = $this->employeeReaderRepository->getEmployeeByUUID($event->uuid->toString());
        $user = $employee->getUser();
        $address = $employee->getAddress();
        $contacts = $employee->getContacts();

        $department = $this->departmentReaderRepository->getDepartmentByUUID($event->departmentUUID->toString());
        $role = $this->roleReaderRepository->getRoleByUUID($event->roleUUID->toString());
        $position = $this->positionReaderRepository->getPositionByUUID($event->positionUUID->toString());
        $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($event->contractTypeUUID->toString());
        $parentEmployee = $event->parentEmployeeUUID?->toString()
            ? $this->employeeReaderRepository->getEmployeeByUUID($event->parentEmployeeUUID->toString())
            : null;

        $this->setEmployeeMainData($employee, $event);
        $this->setEmployeeRelations($employee, $department, $role, $position, $contractType, $parentEmployee, $address, $contacts, $user);

        $this->deleteOldContacts($contacts);
        $this->deleteOldAddress($address);

        $this->setContacts($employee, $event->phones, $event->emails);
        $this->setAddress($employee, $event->address);
        $this->setUser($user, $event->emails->toArray()[0]);

        $this->employeeWriterRepository->saveEmployeeInDB($employee);
    }

    protected function deleteOldContacts(Collection $contacts): void
    {
        $this->contactWriterRepository->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);
    }

    protected function deleteOldAddress(Address $address): void
    {
        $this->addressWriterRepository->deleteAddressInDB($address, Address::HARD_DELETED_AT);
    }

    protected function setContacts(Employee $employee, Phones $phones, Emails $emails): void
    {
        $contacts = $this->createContacts($phones, $emails);
        foreach ($contacts as $contact) {
            $employee->addContact($contact);
        }
    }

    protected function setAddress(Employee $employee, AddressValueObject $addressValueObject): void
    {
        $address = $employee->getAddress();
        $address->setStreet($addressValueObject->getStreet());
        $address->setPostcode($addressValueObject->getPostcode());
        $address->setCity($addressValueObject->getCity());
        $address->setCountry($addressValueObject->getCountry());
        $address->setActive($addressValueObject->getActive());
    }

    protected function setUser(User $user, string $email): void
    {
        if ($user->getEmail() !== $email) {
            $user->setEmail($email);
        }
    }
}
