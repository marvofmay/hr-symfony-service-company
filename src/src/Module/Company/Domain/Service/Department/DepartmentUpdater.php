<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Interface\Address\AddressWriterInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Contact\ContactWriterInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;
use App\Module\Company\Domain\Service\Department\Factory\DepartmentFactory;
use App\Module\Company\Domain\Service\Factory\AddressFactory;
use App\Module\Company\Domain\Service\Factory\ContactFactory;
use Doctrine\Common\Collections\Collection;

final readonly class DepartmentUpdater {

    public function __construct(
        private DepartmentFactory $departmentFactory,
        private AddressFactory $addressFactory,
        private ContactFactory $contactFactory,
        private CompanyReaderInterface $companyReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private DepartmentWriterInterface $departmentWriterRepository,
        private ContactWriterInterface $contactWriterRepository,
        private AddressWriterInterface $addressWriterRepository,
    ) {}

    public function update(DomainEventInterface $event): void
    {
        $department = $this->departmentReaderRepository->getDepartmentByUUID($event->uuid->toString());
        $this->departmentFactory->update($department, $event);

        $this->deleteAddress($department->getAddress());
        $this->deleteContacts($department->getContacts());

        $address = $this->addressFactory->create($event->address);
        $department->setAddress($address);

        $contacts = $this->contactFactory->create($event->phones, $event->emails, $event->websites);
        foreach ($contacts as $contact) {
            $department->addContact($contact);
        }

        $company = $this->companyReaderRepository->getCompanyByUUID($event->companyUUID->toString());
        $department->setCompany($company);

        if (null !== $event->parentDepartmentUUID) {
            $parentDepartment = $this->departmentReaderRepository->getDepartmentByUUID($event->parentDepartmentUUID->toString());
            $department->setParentDepartment($parentDepartment);
        }

        $this->departmentWriterRepository->saveDepartmentInDB($department);
    }

    private function deleteAddress(?Address $address): void
    {
        if ($address !== null) {
            $this->addressWriterRepository->deleteAddressInDB($address, Address::HARD_DELETED_AT);
        }
    }

    private function deleteContacts(Collection $contacts): void
    {
        $this->contactWriterRepository->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);
    }
}
