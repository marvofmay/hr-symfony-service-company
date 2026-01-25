<?php

declare(strict_types=1);

namespace App\Module\Company\Application\DTO\Role;

use App\Common\Domain\Interface\DTOInterface;
use App\Module\Company\Application\DTO\Trait\Role\RoleDTOTrait;

class CreateDTO implements DTOInterface
{
    use RoleDTOTrait;
}
