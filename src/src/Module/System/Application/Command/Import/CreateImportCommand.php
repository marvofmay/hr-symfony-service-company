<?php

declare(strict_types=1);

namespace App\Module\System\Application\Command\Import;

use App\Module\System\Domain\Entity\Import;

class CreateImportCommand
{
    public function __construct(public Import $import,)
    {
    }
}
