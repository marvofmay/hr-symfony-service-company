<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\DTO\AddressDTO;
use App\Common\Domain\Interface\CommandInterface;
use App\Module\Company\Application\Command\Employee\CreateEmployeeCommand;
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

    public function create(CreateEmployeeCommand $command): void
    {
        $this->setEmployee($command);
        $this->employeeWriterRepository->saveEmployeeInDB($this->employee);
    }

    protected function setEmployee(CommandInterface $command): void
    {
        $this->setDepartment($command->departmentUUID);
        $this->setRole($command->roleUUID);
        $this->setPosition($command->positionUUID);
        $this->setContractType($command->contractTypeUUID);
        $this->setParentEmployee($command->parentEmployeeUUID);
        $this->setAddress($command->address);
        $this->setContacts($command->phones);
        $this->setUser($command->email);

        $this->setEmployeeMainData($command);
        $this->setEmployeeRelations();
    }

    protected function setEmployeeMainData(CommandInterface $command): void
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

    protected function setContacts(array $phones, array $emails = [], array $websites = []): void
    {
        $dataSets = [
            ContactTypeEnum::PHONE->value   => $phones,
            ContactTypeEnum::EMAIL->value   => $emails,
            ContactTypeEnum::WEBSITE->value => $websites,
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

    protected function setAddress(AddressDTO $addressDTO): void
    {
        $this->address->setStreet($addressDTO->street);
        $this->address->setPostcode($addressDTO->postcode);
        $this->address->setCity($addressDTO->city);
        $this->address->setCountry($addressDTO->country);
        $this->address->setActive($addressDTO->active);
    }

    protected function setUser(string $email): void
    {
        $user = $this->userFactory->create($email, $email);
        $this->user = $user;
    }
}
