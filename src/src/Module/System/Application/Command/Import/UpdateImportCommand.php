<?php

declare(strict_types=1);

namespace App\Module\System\Application\Command\Import;

use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\Import\ImportStatusEnum;

class UpdateImportCommand
{
    public function __construct(public Import $import, public ImportStatusEnum $importStatusEnum)
    {
    }
}
