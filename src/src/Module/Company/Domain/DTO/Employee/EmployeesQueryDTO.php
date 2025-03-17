<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Employee;

use App\Common\Domain\Abstract\QueryDTOAbstract;
use OpenApi\Attributes as OA;

class EmployeesQueryDTO extends QueryDTOAbstract
{
    #[OA\Property(description: 'Imię pracownika', type: 'string', nullable: true)]
    public ?string $firstName = null;

    #[OA\Property(description: 'Nazwisko pracownika', type: 'string', nullable: true)]
    public ?string $lastName = null;

    #[OA\Property(description: 'Aktywne', type: 'bool', nullable: true)]
    public ?bool $active = null;
}
