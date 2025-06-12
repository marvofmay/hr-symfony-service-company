<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Department;

use App\Common\Domain\Abstract\QueryDTOAbstract;

class DepartmentsQueryDTO extends QueryDTOAbstract
{
    public ?string $name= null;

    public ?string $description = null;

    public ?bool $active = null;
}
