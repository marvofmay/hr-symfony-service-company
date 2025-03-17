<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Department;

use App\Common\Domain\Abstract\QueryDTOAbstract;
use OpenApi\Attributes as OA;

class DepartmentsQueryDTO extends QueryDTOAbstract
{
    #[OA\Property(description: 'Nazwa departamentu', type: 'string', nullable: true)]
    public ?string $name= null;

    #[OA\Property(description: 'Opis departamentu', type: 'string', nullable: true)]
    public ?string $description = null;

    #[OA\Property(description: 'Aktywne', type: 'bool', nullable: true)]
    public ?bool $active = null;
}
