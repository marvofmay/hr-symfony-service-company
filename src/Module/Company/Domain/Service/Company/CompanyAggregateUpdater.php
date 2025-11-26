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
use App\Module\Company\Domain\Enum\CompanyImportColumnEnum;
use App\Module\System\Domain\ValueObject\UserUUID;

final class CompanyAggregateUpdater extends AggregateAbstract
{
    public function update(array $row, ?CompanyUUID $parentUUID, UserUUID $loggedUserUUID): void
    {
        $companyAggregate = $this->companyAggregateReaderRepository->getCompanyAggregateByUUID(
            CompanyUUID::fromString($row[CompanyImportColumnEnum::DYNAMIC_AGGREGATE_UUID->value]),
        );

        $companyAggregate->update(
            FullName::fromString($row[CompanyImportColumnEnum::COMPANY_FULL_NAME->value]),
            NIP::fromString((string) $row[CompanyImportColumnEnum::NIP->value]),
            REGON::fromString((string) $row[CompanyImportColumnEnum::REGON->value]),
            IndustryUUID::fromString($row[CompanyImportColumnEnum::INDUSTRY_UUID->value]),
            (bool) $row[CompanyImportColumnEnum::ACTIVE->value],
            new Address(
                $row[CompanyImportColumnEnum::STREET->value],
                $row[CompanyImportColumnEnum::POSTCODE->value],
                $row[CompanyImportColumnEnum::CITY->value],
                $row[CompanyImportColumnEnum::COUNTRY->value]
            ),
            Phones::fromArray([$row[CompanyImportColumnEnum::PHONE->value]]),
            $loggedUserUUID,
            ShortName::fromString($row[CompanyImportColumnEnum::COMPANY_SHORT_NAME->value]),
            $row[CompanyImportColumnEnum::COMPANY_INTERNAL_CODE->value],
            $row[CompanyImportColumnEnum::COMPANY_DESCRIPTION->value],
            $parentUUID,
            $row[CompanyImportColumnEnum::EMAIL->value] ? Emails::fromArray([$row[CompanyImportColumnEnum::EMAIL->value]]) : null,
            $row[CompanyImportColumnEnum::WEBSITE->value] ? Websites::fromArray([$row[CompanyImportColumnEnum::WEBSITE->value]]) : null,
        );

        $this->commitEvents($companyAggregate->pullEvents(), CompanyAggregate::class);
    }
}
