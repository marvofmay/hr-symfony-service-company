<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Employee;

use App\Common\Domain\DTO\AddressDTO;
use App\Module\Company\Application\Command\Employee\UpdateEmployeeCommand;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\Position;
use App\Module\Company\Domain\Entity\Role;
use App\Module\Company\Domain\Entity\User;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EmployeeUpdater extends EmployeeCreator
{
    //public function __construct(
    //    protected Department                  $department,
    //    protected Employee                    $employee,
    //    protected ?Employee                   $parentEmployee,
    //    protected Role                        $role,
    //    protected Position                    $position,
    //    protected ContractType                $contractType,
    //    protected User                        $user,
    //    protected Address                     $address,
    //    protected EmployeeWriterInterface     $employeeWriterRepository,
    //    protected DepartmentReaderInterface   $departmentReaderRepository,
    //    protected EmployeeReaderInterface     $employeeReaderRepository,
    //    protected ContractTypeReaderInterface $contractTypeReaderRepository,
    //    protected PositionReaderInterface     $positionReaderRepository,
    //    protected RoleReaderInterface         $roleReaderRepository,
    //    protected UserWriterInterface         $userWriterRepository,
    //    protected ContactWriterInterface      $contactWriterRepository,
    //    protected AddressWriterInterface      $addressWriterRepository,
    //    protected UserPasswordHasherInterface $userPasswordHasher,
    //    protected UserFactory                 $userFactory,
    //)
    //{
    //    parent::__construct(
    //        $department,
    //        $employee,
    //        $parentEmployee,
    //        $role,
    //        $position,
    //        $contractType,
    //        $user,
    //        $address,
    //        $employeeWriterRepository,
    //        $departmentReaderRepository,
    //        $employeeReaderRepository,
    //        $contractTypeReaderRepository,
    //        $positionReaderRepository,
    //        $roleReaderRepository,
    //        $userWriterRepository,
    //        $userFactory,
    //    );
    //}
    //
    //public function update(Employee $employee, UpdateEmployeeCommand $command): void
    //{
    //    $this->employee = $employee;
    //    $this->user = $employee->getUser();
    //    $this->setEmployee($command);
    //    $this->employeeWriterRepository->saveEmployeeInDB($this->employee);
    //}
    //
    //protected function setContacts(array $phones, array $emails = [], array $websites = []): void
    //{
    //    $contacts = $this->employee->getContacts(ContactTypeEnum::PHONE);
    //    $this->contactWriterRepository->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);
    //
    //    parent::setContacts($phones);
    //}
    //
    //protected function setAddress(AddressDTO $addressDTO): void
    //{
    //    $address = $this->employee->getAddress();
    //    $this->addressWriterRepository->deleteAddressInDB($address, Address::HARD_DELETED_AT);
    //
    //    parent::setAddress($addressDTO);
    //}
    //
    //protected function setUser(string $email): void
    //{
    //    if (null !== $this->user && $this->user->getEmail() !== $email) {
    //        $this->user->setEmail($email);
    //    }
    //}
}
