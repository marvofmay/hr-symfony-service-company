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
    )
    {
    }

    public function restore(DomainEventInterface $event): void
    {
        $now = new \DateTime();

        $company = $this->companyReaderRepository->getDeletedCompanyByUUID($event->uuid->toString());
        $company->setDeletedAt(null);
        $company->setUpdatedAt($now);

        $address = $this->companyReaderRepository->getDeletedAddressByCompanyByUUID($event->uuid->toString());
        if ($address) {
            $address->setDeletedAt(null);
            $address->setUpdatedAt($now);
        }

        $contacts = $this->companyReaderRepository->getDeletedContactsByCompanyByUUID($event->uuid->toString());
        foreach ($contacts as $contact) {
            $contact->setDeletedAt(null);
            $contact->setUpdatedAt($now);
        }

        $this->companyWriterRepository->saveCompanyInDB($company);
    }
}
