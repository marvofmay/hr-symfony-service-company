<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Company;

use App\Common\Domain\Abstract\QueryDTOAbstract;
use OpenApi\Attributes as OA;

class CompaniesQueryDTO extends QueryDTOAbstract
{
    #[OA\Property(description: 'Pełna nazwa firmy', type: 'string', nullable: true)]
    public ?string $fullName = null;

    #[OA\Property(description: 'Pełna nazwa firmy', type: 'string', nullable: true)]
    public ?string $shortName = null;

    #[OA\Property(description: 'Opis firmy', type: 'string', nullable: true)]
    public ?string $description = null;

    #[OA\Property(description: 'NIP firmy', type: 'string', nullable: true)]
    public ?string $nip = null;

    #[OA\Property(description: 'REGON firmy', type: 'string', nullable: true)]
    public ?string $regon = null;

    #[OA\Property(description: 'Aktywna', type: 'bool', nullable: true)]
    public ?bool $active = null;
}
