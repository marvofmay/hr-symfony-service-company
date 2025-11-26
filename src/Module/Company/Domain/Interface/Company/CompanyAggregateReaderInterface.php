<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Company;

use App\Module\Company\Domain\Aggregate\Company\CompanyAggregate;
use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;

interface CompanyAggregateReaderInterface
{
    public function getCompanyAggregateByUUID(CompanyUUID $uuid): CompanyAggregate;
}
