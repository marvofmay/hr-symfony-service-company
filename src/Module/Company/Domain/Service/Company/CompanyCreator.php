<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Domain\Service\Company\Factory\CompanyFactory;
use App\Module\Company\Domain\Service\Factory\AddressFactory;
use App\Module\Company\Domain\Service\Factory\ContactFactory;

final readonly class CompanyCreator
{
    public function __construct(
        private CompanyFactory $companyFactory,
        private AddressFactory $addressFactory,
        private ContactFactory $contactFactory,
        private CompanyWriterInterface $companyWriterRepository,
        private CompanyReaderInterface $companyReaderRepository,
        private IndustryReaderInterface $industryReaderRepository,
        private EntityReferenceCache $entityReferenceCache,
    ) {
    }

    public function create(DomainEventInterface $event): void
    {
        $company = $this->companyFactory->create($event);
        $address = $this->addressFactory->create($event->address);
        $contacts = $this->contactFactory->create($event->phones, $event->emails, $event->websites);

        $industry = $this->entityReferenceCache->get(
            Industry::class,
            $event->industryUUID->toString(),
            fn (string $uuid) => $this->industryReaderRepository->getIndustryByUUID($uuid)
        );

        $parentCompany = $event->parentCompanyUUID?->toString()
            ? $this->entityReferenceCache->get(
                Company::class,
                $event->parentCompanyUUID->toString(),
                fn (string $uuid) => $this->companyReaderRepository->getCompanyByUUID($uuid)
            )
            : null;

        $this->setCompanyRelations($company, $industry, $parentCompany, $address, $contacts);

        $this->companyWriterRepository->saveCompanyInDB($company);
    }

    private function setCompanyRelations(Company $company, Industry $industry, ?Company $parentCompany, Address $address, array $contacts): void
    {
        $company->setIndustry($industry);
        $company->setParentCompany($parentCompany);
        $company->setAddress($address);

        foreach ($contacts as $contact) {
            $company->addContact($contact);
        }
    }
}
