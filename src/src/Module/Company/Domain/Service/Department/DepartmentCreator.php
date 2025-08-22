<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;
use App\Module\Company\Domain\Service\Department\Factory\DepartmentFactory;
use App\Module\Company\Domain\Service\Factory\AddressFactory;
use App\Module\Company\Domain\Service\Factory\ContactFactory;
use Doctrine\Common\Collections\ArrayCollection;

final readonly class DepartmentCreator
{
    protected ArrayCollection $contacts;

    public function __construct(
        protected DepartmentFactory       $departmentFactory,
        private AddressFactory            $addressFactory,
        private ContactFactory            $contactFactory,
        private CompanyReaderInterface    $companyReaderRepository,
        private DepartmentReaderInterface $departmentReaderRepository,
        private DepartmentWriterInterface $departmentWriterRepository,
    )
    {
        $this->contacts = new ArrayCollection();
    }

    public function create(DomainEventInterface $event): void
    {
        $department = $this->departmentFactory->createFromEvent($event);
        $address = $this->addressFactory->createFromValueObject($event->address);
        $contacts = $this->contactFactory->createContacts($event->phones, $event->emails, $event->websites);
        $department->setAddress($address);

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
}
