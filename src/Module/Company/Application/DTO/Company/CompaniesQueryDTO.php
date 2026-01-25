<?php

declare(strict_types=1);

namespace App\Module\Company\Application\DTO\Company;

use App\Common\Domain\Abstract\QueryDTOAbstract;

class CompaniesQueryDTO extends QueryDTOAbstract
{
    public ?string $fullName = null;

    public ?string $shortName = null;

    public ?string $description = null;

    public ?string $nip = null;

    public ?string $regon = null;

    public ?bool $active = null;
    public ?string $parentCompanyUUID = null;
}
