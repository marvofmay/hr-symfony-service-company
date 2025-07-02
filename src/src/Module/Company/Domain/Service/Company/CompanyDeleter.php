<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Interface\DomainEventInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Company\CompanyWriterInterface;

readonly class CompanyDeleter
{
    public function __construct(private CompanyWriterInterface $companyWriterRepository, private CompanyReaderInterface $companyReaderRepository,)
    {
    }

    public function delete(DomainEventInterface $event): void
    {
        $company = $this->companyReaderRepository->getCompanyByUUID($event->uuid->toString());
        $this->companyWriterRepository->deleteCompanyInDB($company);
    }
}
