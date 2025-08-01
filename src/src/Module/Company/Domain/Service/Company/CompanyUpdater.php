<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Aggregate\ValueObject\Address as AddressValueObject;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\ValueObject\Websites;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Address\AddressWriterInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;
use App\Module\Company\Domain\Interface\Contact\ContactWriterInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;

final class CompanyUpdater extends CompanyCreator
{
    public function __construct(
        protected Company $company,
        protected Address $address,
        protected CompanyWriterInterface $companyWriterRepository,
        protected CompanyReaderInterface $companyReaderRepository,
        protected IndustryReaderInterface $industryReaderRepository,
        protected ContactWriterInterface $contactWriterRepository,
        protected AddressWriterInterface $addressWriterRepository,
    ) {
        parent::__construct($company, $address, $companyWriterRepository, $companyReaderRepository, $industryReaderRepository);
    }

    public function update(DomainEventInterface $event): void
    {
        $company = $this->companyReaderRepository->getCompanyByUUID($event->uuid->toString());

        $this->company = $company;
        $this->setCompany($event);
        $this->setCompanyRelations($event);

        $this->companyWriterRepository->saveCompanyInDB($this->company);
    }

    protected function setContacts(Phones $phones, ?Emails $emails = null, ?Websites $websites = null): void
    {
        foreach ([ContactTypeEnum::PHONE, ContactTypeEnum::EMAIL, ContactTypeEnum::WEBSITE] as $enum) {
            $contacts = $this->company->getContacts($enum);
            $this->contactWriterRepository->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);
        }

        parent::setContacts($phones, $emails, $websites);
    }

    protected function setAddress(AddressValueObject $addressValueObject): void
    {
        $address = $this->company->getAddress();
        $this->addressWriterRepository->deleteAddressInDB($address, Address::HARD_DELETED_AT);

        parent::setAddress($addressValueObject);
    }
}
