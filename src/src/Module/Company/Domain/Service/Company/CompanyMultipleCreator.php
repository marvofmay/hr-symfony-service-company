<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Address\AddressWriterInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;
use App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Contact\Writer\ContactWriterRepository;
use App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Industry\Reader\IndustryReaderRepository;
use Doctrine\Common\Collections\ArrayCollection;

final class CompanyMultipleCreator
{
    private Company $company;

    public function __construct(
        private readonly CompanyWriterInterface $companyWriterRepository,
        private readonly CompanyReaderInterface $companyReaderRepository,
        private readonly IndustryReaderRepository $industryReaderRepository,
        private readonly ContactWriterRepository $contactWriterRepository,
        private readonly AddressWriterInterface $addressWriterRepository,
    ) {
    }

    public function multipleCreate(DomainEventInterface $event): void
    {
        $this->setCompanies($event->rows);
    }

    private function setCompanies(array $data): void
    {
        $companies = new ArrayCollection();
        $temporaryCompanyMap = [];

        foreach ($data as $item) {
            $this->setCompany($item);
            $this->setMainCompanyData($item);
            $this->setAddress($item);
            $this->setContacts($item);

            if (is_int($item[ImportCompaniesFromXLSX::COLUMN_COMPANY_UUID])) {
                $temporaryCompanyMap[$item[ImportCompaniesFromXLSX::COLUMN_COMPANY_UUID]] = $this->company;
            }

            $companies[] = $this->company;
        }

        foreach ($data as $index => $item) {
            $company = $companies[$index];
            $this->company = $company;

            $this->setRelations($item, $temporaryCompanyMap);
        }

        $this->companyWriterRepository->saveCompaniesInDB($companies);
    }

    private function setCompany(array $item): void
    {
        if ((null === $item[ImportCompaniesFromXLSX::COLUMN_COMPANY_UUID] || is_int($item[ImportCompaniesFromXLSX::COLUMN_COMPANY_UUID])) && isset($item['_aggregate_uuid'])) {
            $this->company = new Company();
            $this->company->setUUID($item['_aggregate_uuid']);
        } elseif (is_string($item[ImportCompaniesFromXLSX::COLUMN_COMPANY_UUID])) {
            $company = $this->companyReaderRepository->getCompanyByUUID($item[ImportCompaniesFromXLSX::COLUMN_COMPANY_UUID]);
            if (null === $company) {
                $this->company = new Company();
                if (isset($item['_aggregate_uuid'])) {
                    $this->company->setUUID($item['_aggregate_uuid']);
                }
            } else {
                $this->company = $company;
            }
        }
    }

    private function setMainCompanyData(array $item): void
    {
        $this->company->setFullName($item[ImportCompaniesFromXLSX::COLUMN_COMPANY_FULL_NAME]);
        $this->company->setShortName($item[ImportCompaniesFromXLSX::COLUMN_COMPANY_SHORT_NAME]);
        $this->company->setDescription($item[ImportCompaniesFromXLSX::COLUMN_COMPANY_DESCRIPTION]);
        $this->company->setNip((string) $item[ImportCompaniesFromXLSX::COLUMN_NIP]);
        $this->company->setRegon((string) $item[ImportCompaniesFromXLSX::COLUMN_REGON]);
        $this->company->setActive((bool) $item[ImportCompaniesFromXLSX::COLUMN_ACTIVE]);
    }

    private function setAddress(array $item): void
    {
        if (null !== $item[ImportCompaniesFromXLSX::COLUMN_COMPANY_UUID] && is_string($item[ImportCompaniesFromXLSX::COLUMN_COMPANY_UUID])) {
            $address = $this->company->getAddress();
            $this->addressWriterRepository->deleteAddressInDB($address, Address::HARD_DELETED_AT);
        }

        $address = new Address();
        $address->setStreet($item[ImportCompaniesFromXLSX::COLUMN_STREET]);
        $address->setPostcode($item[ImportCompaniesFromXLSX::COLUMN_POSTCODE]);
        $address->setCity($item[ImportCompaniesFromXLSX::COLUMN_CITY]);
        $address->setCountry($item[ImportCompaniesFromXLSX::COLUMN_COUNTRY]);

        $this->company->setAddress($address);
    }

    private function setContacts(array $item): void
    {
        if (null !== $item[ImportCompaniesFromXLSX::COLUMN_COMPANY_UUID]) {
            foreach ([ContactTypeEnum::PHONE, ContactTypeEnum::EMAIL, ContactTypeEnum::WEBSITE] as $enum) {
                $contacts = $this->company->getContacts($enum);
                $this->contactWriterRepository->deleteContactsInDB($contacts, Contact::HARD_DELETED_AT);
            }
        }

        if (null !== $item[ImportCompaniesFromXLSX::COLUMN_PHONE]) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::PHONE->value);
            $contact->setData($item[ImportCompaniesFromXLSX::COLUMN_PHONE]);
            $contact->setActive(true);
            $this->company->addContact($contact);
        }

        if (null !== $item[ImportCompaniesFromXLSX::COLUMN_EMAIL]) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::EMAIL->value);
            $contact->setData((string) $item[ImportCompaniesFromXLSX::COLUMN_EMAIL]);
            $contact->setActive(true);
            $this->company->addContact($contact);
        }

        if (null !== $item[ImportCompaniesFromXLSX::COLUMN_WEBSITE]) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::WEBSITE->value);
            $contact->setData($item[ImportCompaniesFromXLSX::COLUMN_WEBSITE]);
            $contact->setActive(true);
            $this->company->addContact($contact);
        }
    }

    private function setRelations(array $item, array $temporaryCompanyMap): void
    {
        $industry = $this->industryReaderRepository->getIndustryByUUID($item[ImportCompaniesFromXLSX::COLUMN_INDUSTRY_UUID]);
        if ($industry instanceof Industry) {
            $this->company->setIndustry($industry);
        }

        $parentCompanyUUID = $item[ImportCompaniesFromXLSX::COLUMN_PARENT_COMPANY_NIP] ?? null;

        if (null !== $parentCompanyUUID) {
            if (is_int($parentCompanyUUID) && isset($temporaryCompanyMap[$parentCompanyUUID])) {
                $this->company->setParentCompany($temporaryCompanyMap[$parentCompanyUUID]);
            } elseif (is_string($parentCompanyUUID)) {
                $parentCompany = $this->companyReaderRepository->getCompanyByUUID($parentCompanyUUID);
                if ($parentCompany instanceof Company) {
                    $this->company->setParentCompany($parentCompany);
                }
            }
        }
    }
}
