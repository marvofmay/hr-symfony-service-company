<?php

declare(strict_types=1);

namespace App\Module\System\Application\Command\Import;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Enum\ImportKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;

class CreateImportCommand
{
    public function __construct(public ImportKindEnum $kindEnum, public ImportStatusEnum $statusEnum, public File $file, public ?Employee $employee = null)
    {
    }
}
