<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Role\Import;

interface RolesImporterInterface
{
    public function save(array $preparedRows, array $existingRoles): void;
}