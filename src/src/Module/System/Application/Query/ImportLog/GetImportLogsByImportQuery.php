<?php

declare(strict_types=1);

namespace App\Module\System\Application\Query\ImportLog;

use App\Module\System\Domain\Entity\Import;

final class GetImportLogsByImportQuery
{
    public function __construct(public Import $import,) {}
}