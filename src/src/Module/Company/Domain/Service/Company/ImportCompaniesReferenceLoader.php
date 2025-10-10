<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;

final class ImportCompaniesReferenceLoader
{
    private array $industries = [];
    private array $companies = [];

    public function __construct(
        private readonly IndustryReaderInterface $industryReaderRepository,
        private readonly CompanyReaderInterface $companyReaderRepository,
    ) {
    }

    public function preload(array $rows): void
    {
        $industryUUIDs = [];
        $companyNIPs = [];

        foreach ($rows as $row) {
            if (!empty($row[ImportCompaniesFromXLSX::COLUMN_INDUSTRY_UUID])) {
                $industryUUIDs[] = (string) $row[ImportCompaniesFromXLSX::COLUMN_INDUSTRY_UUID];
            }
            if (!empty($row[ImportCompaniesFromXLSX::COLUMN_PARENT_COMPANY_NIP])) {
                $companyNIPs[] = (string) $row[ImportCompaniesFromXLSX::COLUMN_PARENT_COMPANY_NIP];
            }
        }

        $industryUUIDs = array_unique($industryUUIDs);
        $companyNIPs = array_unique($companyNIPs);

        $this->industries = $this->mapByUUID($this->industryReaderRepository->getIndustriesByUUID($industryUUIDs));
        $this->companies = $this->mapByNIP($this->companyReaderRepository->getCompaniesByNIP($companyNIPs));
    }

    public function getIndustries(): array
    {
        return $this->industries;
    }

    public function getCompanies(): array
    {
        return $this->companies;
    }

    private function mapByUUID(iterable $entities): array
    {
        $map = [];
        foreach ($entities as $entity) {
            $map[$entity->getUUID()->toString()] = $entity;
        }

        return $map;
    }

    private function mapByNIP(iterable $companies): array
    {
        $map = [];
        foreach ($companies as $company) {
            $map[$company->getNIP()] = $company;
        }

        return $map;
    }
}
