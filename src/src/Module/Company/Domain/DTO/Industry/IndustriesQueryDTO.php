<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Industry;

use App\Common\Domain\Abstract\QueryDTOAbstract;
use OpenApi\Attributes as OA;

class IndustriesQueryDTO extends QueryDTOAbstract
{
    #[OA\Property(description: 'Nazwa branży', type: 'string', nullable: true)]
    public ?string $name = null;

    #[OA\Property(description: 'Opis branży', type: 'string', nullable: true)]
    public ?string $description = null;
}
