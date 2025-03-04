<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\DTO\AddressDTO;
use App\Module\Company\Application\Command\Employee\UpdateEmployeeCommand;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\User;
use App\Module\Company\Domain\Enum\ContactTypeEnum;

class EmployeeUpdater extends EmployeeCreator
{
    public function update(Employee $employee, UpdateEmployeeCommand $command): void
    {
        $this->employee = $employee;
        $this->setEmployee($command);
        $this->employeeWriterRepository->updateEmployeeInDB($this->employee);
    }

    protected function setContacts(array $phones): void
    {
        $contacts = $this->employee->getContacts(ContactTypeEnum::PHONE);
        $this->contactWriterRepository->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);

        parent::setContacts($phones);
    }

    protected function setAddress(AddressDTO $addressDTO): void
    {
        $address = $this->employee->getAddress();
        $this->addressWriterRepository->deleteAddressInDB($address, Address::HARD_DELETED_AT);

        parent::setAddress($addressDTO);
    }

    protected function setUser(string $email, string $firstName): void
    {
        $password = sprintf('%s-%s', $email, $firstName);

        if (null !== $this->employee->getUser() && $this->employee->getUser()->getEmail() !== $email) {
            $this->userWriterRepository->deleteUserInDB($this->employee->getUser(), User::HARD_DELETED_AT);
            $this->user->setEmail($email);
            $this->user->setPassword($password);
        }
    }
}