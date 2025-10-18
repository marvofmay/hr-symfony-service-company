<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;

final readonly class CompanyRestorer
{
    public function __construct(
        private CompanyWriterInterface $companyWriterRepository,
        private CompanyReaderInterface $companyReaderRepository,
    ) {
    }

    public function restore(DomainEventInterface $event): void
    {
        $company = $this->companyReaderRepository->getDeletedCompanyByUUID($event->uuid->toString());
        $company->setDeletedAt(null);

        $address = $this->companyReaderRepository->getDeletedAddressByCompanyUUID($event->uuid->toString());
        if ($address) {
            $address->setDeletedAt(null);
        }

        $contacts = $this->companyReaderRepository->getDeletedContactsByCompanyUUID($event->uuid->toString());
        foreach ($contacts as $contact) {
            $contact->setDeletedAt(null);
        }

        $this->companyWriterRepository->saveCompanyInDB($company);
    }
}
