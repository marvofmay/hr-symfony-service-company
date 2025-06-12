<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use App\Common\Domain\Abstract\QueryDTOAbstract;

class RolesQueryDTO extends QueryDTOAbstract
{
    public ?string $name = null;

    public ?string $description = null;
}
