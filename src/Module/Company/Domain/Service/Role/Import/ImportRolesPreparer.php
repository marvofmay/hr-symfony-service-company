<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role\Import;

use App\Module\Company\Domain\Enum\Role\RoleImportColumnEnum;

final readonly class ImportRolesPreparer
{
    public function prepare(iterable $rows, array $existingRoles): array
    {
        $preparedRows = [];
        foreach ($rows as $row) {
            $name = trim((string) $row[RoleImportColumnEnum::ROLE_NAME->value]);
            $row[RoleImportColumnEnum::DYNAMIC_IS_ROLE_WITH_NAME_ALREADY_EXISTS->value] = $existingRoles[$name] ?? false;
            $preparedRows[] = $row;
        }

        return $preparedRows;
    }
}
