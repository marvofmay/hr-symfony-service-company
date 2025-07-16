<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Department;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Address as AddressValueObject;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\Websites;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

class DepartmentCreator
{
    protected ArrayCollection $contacts;

    public function __construct(
        protected Department $department,
        protected Address $address,
        protected CompanyReaderInterface $companyReaderRepository,
        protected DepartmentReaderInterface $departmentReaderRepository,
        protected DepartmentWriterInterface $departmentWriterRepository,
    ) {
        $this->contacts = new ArrayCollection();
    }

    public function create(DomainEventInterface $event): void
    {
        $this->setDepartment($event);
        $this->departmentWriterRepository->saveDepartmentInDB($this->department);
    }

    protected function setDepartment(DomainEventInterface $event): void
    {
        $this->setAddress($event->address);
        $this->setContacts($event->phones, $event->emails, $event->websites);

        $this->setDepartmentMainData($event);
        $this->setDepartmentRelations($event);
    }

    protected function setDepartmentMainData(DomainEventInterface $event): void
    {
        $this->department->setUUID($event->uuid->toString());
        $this->department->setName($event->name->getValue());
        $this->department->setDescription($event->description);
        $this->department->setActive($event->active);
    }

    protected function setDepartmentRelations(DomainEventInterface $event): void
    {
        $company = $this->companyReaderRepository->getCompanyByUUID($event->companyUUID->toString());
        $this->department->setCompany($company);

        if (null !== $event->parentDepartmentUUID) {
            $parentDepartment = $this->departmentReaderRepository->getDepartmentByUUID($event->parentDepartmentUUID->toString());
            $this->department->setParentDepartment($parentDepartment);
        }

        foreach ($this->contacts as $contact) {
            $this->department->addContact($contact);
        }

        $this->department->setAddress($this->address);
    }

    protected function setContacts(Phones $phones, ?Emails $emails = null, ?Websites $websites = null): void
    {
        $dataSets = [
            ContactTypeEnum::PHONE->value => $phones->toArray(),
            ContactTypeEnum::EMAIL->value => $emails->toArray(),
            ContactTypeEnum::WEBSITE->value => $websites->toArray(),
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

    protected function setAddress(AddressValueObject $addressValueObject): void
    {
        $this->address->setStreet($addressValueObject->getStreet());
        $this->address->setPostcode($addressValueObject->getPostcode());
        $this->address->setCity($addressValueObject->getCity());
        $this->address->setCountry($addressValueObject->getCountry());
        $this->address->setActive($addressValueObject->getActive());
    }
}
