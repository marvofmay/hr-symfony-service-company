<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;

final readonly class CompanyMultipleDeleter
{
    public function __construct(
        private CompanyWriterInterface $companyWriterRepository,
        private CompanyReaderInterface $companyReaderRepository,
    ) {
    }

    public function multipleDelete(DomainEventInterface $event): void
    {
        $this->companyWriterRepository->deleteMultipleCompaniesInDB(
            $this->companyReaderRepository->getCompaniesByUUID($event->uuids)
        );
    }
}
