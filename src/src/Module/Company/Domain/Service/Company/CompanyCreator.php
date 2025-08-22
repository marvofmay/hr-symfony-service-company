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
        private CompanyWriterInterface $companyWriter,
        private CompanyReaderInterface $companyReader,
        private IndustryReaderInterface $industryReader,
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
}
