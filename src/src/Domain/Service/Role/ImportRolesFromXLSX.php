<?php

declare(strict_types=1);

namespace App\Domain\Service\Role;

use App\Domain\Service\XLSXIterator;

class ImportRolesFromXLSX extends XLSXIterator
{
    public function validateRow(array $row): ?string
    {
        [$roleName] = $row + [null, null];

        if (empty($roleName)) {
            return "Brak nazwy roli w wierszu " . (count($this->errors) + 2);
        }

        if (strlen($roleName) < 3) {
            return "Nazwa roli powinna mieć min. 3 znaki w wierszu " . (count($this->errors) + 2);
        }

        return null;
    }
}
