<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Query\Company;

use App\Common\Domain\Interface\QueryDTOInterface;
use App\Common\Domain\Interface\QueryInterface;

final class GetAvailableParentCompanyOptionsQuery implements QueryInterface
{
    public ?string $companyUUID = null;

    public function __construct(QueryDTOInterface $queryDTO)
    {
        $this->companyUUID = $queryDTO->companyUUID;
    }
}
