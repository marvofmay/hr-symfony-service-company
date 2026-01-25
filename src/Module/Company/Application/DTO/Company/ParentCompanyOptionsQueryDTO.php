<?php

declare(strict_types=1);

namespace App\Module\Company\Application\DTO\Company;

use App\Common\Domain\Interface\QueryDTOInterface;

class ParentCompanyOptionsQueryDTO implements QueryDTOInterface
{
    public ?string $companyUUID = null;
}
