<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Company;

use App\Common\Domain\Interface\QueryInterface;

final class GetCompanyByUUIDQuery implements QueryInterface
{
    public function __construct(public string $companyUUID)
    {
    }
}
