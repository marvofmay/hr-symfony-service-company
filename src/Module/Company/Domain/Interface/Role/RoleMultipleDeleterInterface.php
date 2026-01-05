<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role;

use Doctrine\Common\Collections\Collection;

interface RoleMultipleDeleterInterface
{
    public function multipleDelete(Collection $roles): void;
}
