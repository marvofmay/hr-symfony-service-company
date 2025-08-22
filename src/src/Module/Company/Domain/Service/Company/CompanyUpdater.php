<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Address\AddressWriterInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;
use App\Module\Company\Domain\Interface\Contact\ContactWriterInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Domain\Service\Company\Factory\CompanyFactory;
use App\Module\Company\Domain\Service\Factory\AddressFactory;
use App\Module\Company\Domain\Service\Factory\ContactFactory;

final class CompanyUpdater
{
    public function __construct(
        private CompanyFactory $companyFactory,
        private AddressFactory $addressFactory,
        private ContactFactory $contactFactory,
        private CompanyWriterInterface $companyWriter,
        private CompanyReaderInterface $companyReader,
        private IndustryReaderInterface $industryReader,
        private ContactWriterInterface $contactWriter,
        private AddressWriterInterface $addressWriter,
    ) {}

    public function update(DomainEventInterface $event): void
    {
        $company = $this->companyReader->getCompanyByUUID($event->uuid->toString());
        $this->companyFactory->updateFromEvent($company, $event);

        $this->deleteAddress($company->getAddress());
        $this->deleteContacts($company);

        $address = $this->addressFactory->createFromValueObject($event->address);
        $company->setAddress($address);

        $contacts = $this->contactFactory->createContacts($event->phones, $event->emails, $event->websites);
        foreach ($contacts as $contact) {
            $company->addContact($contact);
        }

        if ($event->industryUUID) {
            $industry = $this->industryReader->getIndustryByUUID($event->industryUUID->toString());
            if ($industry) {
                $company->setIndustry($industry);
            }
        }

        if ($event->parentCompanyUUID) {
            $parent = $this->companyReader->getCompanyByUUID($event->parentCompanyUUID->toString());
            if ($parent) {
                $company->setParentCompany($parent);
            }
        }

        $this->companyWriter->saveCompanyInDB($company);
    }

    private function deleteAddress(?Address $address): void
    {
        if ($address !== null) {
            $this->addressWriter->deleteAddressInDB($address, Address::HARD_DELETED_AT);
        }
    }

    private function deleteContacts($company): void
    {
        foreach (ContactTypeEnum::communicationTypes() as $type) {
            $contacts = $company->getContacts($type);
            $this->contactWriter->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);
        }
    }
}
