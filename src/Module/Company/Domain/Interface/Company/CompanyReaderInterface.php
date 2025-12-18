<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Company;

use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use Doctrine\Common\Collections\Collection;

interface CompanyReaderInterface
{
    public function getCompanyByUUID(string $uuid): Company;

    public function getCompaniesByUUID(array $selectedUUIDs): Collection;

    public function getCompanyByFullName(string $fullName, ?string $uuid): ?Company;

    public function getCompanyByInternalCode(string $internalCode, ?string $uuid): ?Company;

    public function getCompanyByShortName(string $shortName, ?string $uuid): ?Company;

    public function getCompanyByNIP(string $nip, ?string $uuid): ?Company;

    public function getCompaniesByNIP(array $selectedNIP): Collection;

    public function getCompanyByREGON(string $regon, ?string $uuid): ?Company;

    public function isCompanyExistsWithFullName(string $name, ?string $uuid = null): bool;

    public function isCompanyExistsWithInternalCode(string $internalCode, ?string $uuid = null): bool;

    public function isCompanyExistsWithNIP(string $nip, ?string $uuid = null): bool;

    public function isCompanyExistsWithREGON(string $regon, ?string $uuid = null): bool;

    public function isCompanyExistsWithUUID(string $uuid): bool;

    public function isCompanyExists(string $nip, string $regon, ?string $companyUUID = null): bool;

    public function getDeletedCompanyByUUID(string $uuid): ?Company;

    public function getDeletedAddressByCompanyUUID(string $uuid): ?Address;

    public function getDeletedContactsByCompanyUUID(string $uuid): Collection;

    public function getCompaniesNIPByEmails(array $emails): Collection;

    public function getAllDescendantUUIDs(string $parentUuid): array;
}
