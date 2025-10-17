<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Infrastructure\Cache\EntityReferenceCache;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Enum\CompanyImportColumnEnum;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;

final class ImportCompaniesReferenceLoader
{
    public array $industries = [] {
        get {
            return $this->industries;
        }
    }
    public array $companies = [] {
        get {
            return $this->companies;
        }
    }
    public array $emailsNIPs = [] {
        get {
            return $this->emailsNIPs;
        }
    }

    public function __construct(
        private readonly IndustryReaderInterface $industryReaderRepository,
        private readonly CompanyReaderInterface $companyReaderRepository,
        private readonly EntityReferenceCache $entityReferenceCache,
    ) {
    }

    public function preload(array $rows): void
    {
        $industryUUIDs = [];
        $companyNIPs = [];
        $companyEmails = [];

        foreach ($rows as $row) {
            if (!empty($row[CompanyImportColumnEnum::INDUSTRY_UUID->value])) {
                $industryUUIDs[] = (string) $row[CompanyImportColumnEnum::INDUSTRY_UUID->value];
            }
            if (!empty($row[CompanyImportColumnEnum::NIP->value])) {
                $companyNIPs[] = (string) $row[CompanyImportColumnEnum::NIP->value];
            }
            if (!empty($row[CompanyImportColumnEnum::PARENT_COMPANY_NIP->value])) {
                $companyNIPs[] = (string) $row[CompanyImportColumnEnum::PARENT_COMPANY_NIP->value];
            }
            if (!empty($row[CompanyImportColumnEnum::EMAIL->value])) {
                $companyEmails[] = (string) $row[CompanyImportColumnEnum::EMAIL->value];
            }
        }

        $industryUUIDs = array_unique($industryUUIDs);
        $companyNIPs = array_unique($companyNIPs);
        $companyEmails = array_unique($companyEmails);

        $this->industries = $this->mapByUUID($this->industryReaderRepository->getIndustriesByUUID($industryUUIDs));
        $this->companies = $this->mapByNIP($this->companyReaderRepository->getCompaniesByNIP($companyNIPs));
        $this->emailsNIPs = $this->mapByEmail($this->companyReaderRepository->getCompaniesNIPByEmails($companyEmails));
    }

    private function mapByUUID(iterable $industries): array
    {
        $map = [];
        foreach ($industries as $industry) {
            $map[$industry->getUUID()->toString()] = $industry;
            $this->entityReferenceCache->set($industry);
        }

        return $map;
    }

    private function mapByNIP(iterable $companies): array
    {
        $map = [];
        foreach ($companies as $company) {
            $map[$company->getNIP()] = $company;
            $this->entityReferenceCache->set($company);
        }

        return $map;
    }

    private function mapByEmail(iterable $items): array
    {
        $map = [];
        foreach ($items as $item) {
            $map[$item[ContactTypeEnum::EMAIL->value]] = $item[Company::COLUMN_NIP];
        }

        return $map;
    }
}
