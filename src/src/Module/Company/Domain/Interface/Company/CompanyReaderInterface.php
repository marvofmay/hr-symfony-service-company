<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Company;

use App\Module\Company\Domain\Entity\Company;

interface CompanyReaderInterface
{
    public function getCompanyByUUID(string $uuid): ?Company;
    public function getCompanyByFullName(string $fullName, ?string $uuid): ?Company;
    public function getCompanyByShortName(string $shortName, ?string $uuid): ?Company;
    public function getCompanyByNIP(string $nip, ?string $uuid): ?Company;
    public function getCompanyByREGON(string $regon, ?string $uuid): ?Company;
    public function isCompanyExistsWithFullName(string $name, ?string $uuid = null): bool;
    public function isCompanyExistsWithNIP(string $nip, ?string $uuid = null): bool;
    public function isCompanyExistsWithREGON(string $regon, ?string $uuid = null): bool;
    public function isCompanyExistsWithUUID(string $uuid): bool;
}
