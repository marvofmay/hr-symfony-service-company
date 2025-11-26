<?php

declare(strict_types=1);

namespace App\Module\System\Application\Command\File;

use App\Module\System\Domain\Entity\File;

class CreateFileCommand
{
    public function __construct(public File $file)
    {
    }
}
