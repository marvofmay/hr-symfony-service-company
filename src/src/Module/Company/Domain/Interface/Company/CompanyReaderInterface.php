<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Company;

use App\Module\Company\Domain\Entity\Company;

interface CompanyReaderInterface
{
    public function getCompanyByUUID(string $uuid): ?Company;
    public function getCompanyByFullName(string $fullName, ?string $uuid): ?Company;
    public function getCompanyByShortName(string $shortName, ?string $uuid): ?Company;
}
