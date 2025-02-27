<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Position;

class ImportPositionsCommand
{
    public function __construct(public array $data)
    {
    }
}
