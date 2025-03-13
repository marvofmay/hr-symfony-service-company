<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Position;

use App\Common\Domain\Abstract\QueryDTOAbstract;
use OpenApi\Attributes as OA;

class PositionsQueryDTO extends QueryDTOAbstract
{
    #[OA\Property(description: 'Nazwa stanowiska', type: 'string', nullable: true)]
    public ?string $name = null;

    #[OA\Property(description: 'Opis stanowiska', type: 'string', nullable: true)]
    public ?string $description = null;

    #[OA\Property(description: 'Aktywne', type: 'bool', nullable: true)]
    public ?bool $active = null;
}
