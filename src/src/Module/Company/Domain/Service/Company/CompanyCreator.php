<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Domain\Service\Company\Factory\CompanyFactory;
use App\Module\Company\Domain\Service\Factory\AddressFactory;
use App\Module\Company\Domain\Service\Factory\ContactFactory;

class CompanyCreator
{
    public function __construct(
        private CompanyFactory $companyFactory,
        private AddressFactory $addressFactory,
        private ContactFactory $contactFactory,
        private CompanyWriterInterface $companyWriterRepository,
        private CompanyReaderInterface $companyReaderRepository,
        private IndustryReaderInterface $industryReaderRepository,
    ) {}

    public function create(DomainEventInterface $event): void
    {
        $company = $this->companyFactory->create($event);
        $address = $this->addressFactory->create($event->address);
        $contacts = $this->contactFactory->create($event->phones, $event->emails, $event->websites);
        $company->setAddress($address);
        foreach ($contacts as $contact) {
            $company->addContact($contact);
        }

        if ($event->industryUUID) {
            $industry = $this->industryReaderRepository->getIndustryByUUID($event->industryUUID->toString());
            if ($industry) {
                $company->setIndustry($industry);
            }
        }

        if ($event->parentCompanyUUID) {
            $parent = $this->companyReaderRepository->getCompanyByUUID($event->parentCompanyUUID->toString());
            if ($parent) {
                $company->setParentCompany($parent);
            }
        }

        $this->companyWriterRepository->saveCompanyInDB($company);
    }
}
