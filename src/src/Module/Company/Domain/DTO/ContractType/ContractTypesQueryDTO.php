<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\ContractType;

use App\Common\Domain\Abstract\QueryDTOAbstract;
use OpenApi\Attributes as OA;

class ContractTypesQueryDTO extends QueryDTOAbstract
{
    #[OA\Property(description: 'Nazwa formy zatrudnienia', type: 'string', nullable: true)]
    public ?string $name = null;

    #[OA\Property(description: 'Opis formy zatrudnienia', type: 'string', nullable: true)]
    public ?string $description = null;

    #[OA\Property(description: 'Czy forma zatrudnienia jest aktywna', type: 'bool', nullable: true)]
    public ?bool $active = null;
}
