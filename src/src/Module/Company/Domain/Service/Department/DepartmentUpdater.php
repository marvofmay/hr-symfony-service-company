<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Domain\DTO\AddressDTO;
use App\Module\Company\Application\Command\Department\UpdateDepartmentCommand;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Address\AddressWriterInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Contact\ContactWriterInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;

class DepartmentUpdater extends DepartmentCreator
{
    //public function __construct(
    //    protected Company $company,
    //    protected Department $department,
    //    protected ?Department $parentDepartment,
    //    protected Address $address,
    //    protected CompanyReaderInterface $companyReaderRepository,
    //    protected DepartmentReaderInterface $departmentReaderRepository,
    //    protected DepartmentWriterInterface $departmentWriterRepository,
    //    protected ContactWriterInterface $contactWriterRepository,
    //    protected AddressWriterInterface $addressWriterRepository,
    //) {
    //    parent::__construct($company, $department, $parentDepartment, $address, $companyReaderRepository, $departmentReaderRepository, $departmentWriterRepository);
    //}
    //
    //public function update(UpdateDepartmentCommand $command): void
    //{
    //    $this->department = $command->department;
    //    $this->setDepartment($command);
    //    $this->departmentWriterRepository->saveDepartmentInDB($this->department);
    //}
    //
    //protected function setContacts(array $phones, array $emails = [], array $websites = []): void
    //{
    //    foreach ([ContactTypeEnum::PHONE, ContactTypeEnum::EMAIL, ContactTypeEnum::WEBSITE] as $enum) {
    //        $contacts = $this->department->getContacts($enum);
    //        $this->contactWriterRepository->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);
    //    }
    //
    //    parent::setContacts($phones, $emails, $websites);
    //}
    //
    //protected function setAddress(AddressDTO $addressDTO): void
    //{
    //    $address = $this->department->getAddress();
    //    $this->addressWriterRepository->deleteAddressInDB($address, Address::HARD_DELETED_AT);
    //
    //    parent::setAddress($addressDTO);
    //}
}
