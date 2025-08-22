<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Company;

use App\Common\Domain\Abstract\AggregateAbstract;
use App\Module\Company\Domain\Aggregate\Company\CompanyAggregate;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\FullName;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\IndustryUUID;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\NIP;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\REGON;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\ShortName;
use App\Module\Company\Domain\Aggregate\ValueObject\Address;
use App\Module\Company\Domain\Aggregate\ValueObject\Emails;
use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use App\Module\Company\Domain\Aggregate\ValueObject\Websites;

final class CompanyAggregateUpdater extends AggregateAbstract
{
    public function update(array $row, CompanyUUID $uuid, ?CompanyUUID $parentUUID): void
    {
        $companyAggregate = $this->companyAggregateReaderRepository->getCompanyAggregateByUUID(
            CompanyUUID::fromString($row['_aggregate_uuid'])
        );

        $companyAggregate->update(
            FullName::fromString($row[ImportCompaniesFromXLSX::COLUMN_COMPANY_FULL_NAME]),
            NIP::fromString((string)$row[ImportCompaniesFromXLSX::COLUMN_NIP]),
            REGON::fromString((string)$row[ImportCompaniesFromXLSX::COLUMN_REGON]),
            IndustryUUID::fromString($row[ImportCompaniesFromXLSX::COLUMN_INDUSTRY_UUID]),
            (bool)$row[ImportCompaniesFromXLSX::COLUMN_ACTIVE],
            new Address(
                $row[ImportCompaniesFromXLSX::COLUMN_STREET],
                $row[ImportCompaniesFromXLSX::COLUMN_POSTCODE],
                $row[ImportCompaniesFromXLSX::COLUMN_CITY],
                $row[ImportCompaniesFromXLSX::COLUMN_COUNTRY]
            ),
            Phones::fromArray([$row[ImportCompaniesFromXLSX::COLUMN_PHONE]]),
            ShortName::fromString($row[ImportCompaniesFromXLSX::COLUMN_COMPANY_SHORT_NAME]),
            $row[ImportCompaniesFromXLSX::COLUMN_COMPANY_INTERNAL_CODE],
            $row[ImportCompaniesFromXLSX::COLUMN_COMPANY_DESCRIPTION],
            $parentUUID,
            $row[ImportCompaniesFromXLSX::COLUMN_EMAIL] ? Emails::fromArray([$row[ImportCompaniesFromXLSX::COLUMN_EMAIL]]) : null,
            $row[ImportCompaniesFromXLSX::COLUMN_WEBSITE] ? Websites::fromArray([$row[ImportCompaniesFromXLSX::COLUMN_WEBSITE]]) : null,
        );

        $this->commitEvents($companyAggregate->pullEvents(), CompanyAggregate::class);
    }
}