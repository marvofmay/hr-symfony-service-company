<?php

declare(strict_types=1);

namespace App\Module\System\Application\Query\Import;

use App\Module\System\Domain\Entity\File;

final class GetImportByFileQuery
{
    public function __construct(public File $file)
    {
    }
}
