<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use App\Common\Domain\Abstract\QueryDTOAbstract;
use OpenApi\Attributes as OA;

class RolesQueryDTO extends QueryDTOAbstract
{
    #[OA\Property(description: 'Nazwa roli', type: 'string', nullable: true)]
    public ?string $name = null;

    #[OA\Property(description: 'Opis roli', type: 'string', nullable: true)]
    public ?string $description = null;
}
