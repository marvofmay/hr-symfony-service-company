<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company;

use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;

final readonly class CompanyValidator
{
    public function __construct(private CompanyReaderInterface $companyReaderRepository)
    {
    }

    public function isCompanyExists(string $uuid): void
    {
        $this->companyReaderRepository->getCompanyByUUID($uuid);
    }
}
