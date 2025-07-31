<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\ValueObject\Address as AddressValueObject;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\ValueObject\Websites;
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
    public function __construct(
        protected Company $company,
        protected Department $department,
        protected ?Department $parentDepartment,
        protected Address $address,
        protected CompanyReaderInterface $companyReaderRepository,
        protected DepartmentReaderInterface $departmentReaderRepository,
        protected DepartmentWriterInterface $departmentWriterRepository,
        protected ContactWriterInterface $contactWriterRepository,
        protected AddressWriterInterface $addressWriterRepository,
    ) {
        parent::__construct($department, $address, $companyReaderRepository, $departmentReaderRepository, $departmentWriterRepository);
    }

    public function update(DomainEventInterface $event): void
    {
        $department = $this->departmentReaderRepository->getDepartmentByUUID($event->uuid->toString());
        $this->department = $department;
        $this->setDepartment($event);
        $this->departmentWriterRepository->saveDepartmentInDB($this->department);
    }

    protected function setContacts(Phones $phones, ?Emails $emails = null, ?Websites $websites = null): void
    {
        foreach ([ContactTypeEnum::PHONE, ContactTypeEnum::EMAIL, ContactTypeEnum::WEBSITE] as $enum) {
            $contacts = $this->department->getContacts($enum);
            $this->contactWriterRepository->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);
        }

        parent::setContacts($phones, $emails, $websites);
    }

    protected function setAddress(AddressValueObject $addressValueObject): void
    {
        $address = $this->department->getAddress();
        $this->addressWriterRepository->deleteAddressInDB($address, Address::HARD_DELETED_AT);

        parent::setAddress($addressValueObject);
    }
}
