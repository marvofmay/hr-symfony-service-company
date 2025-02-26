<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Industry;

class ImportIndustriesCommand
{
    public function __construct(public array $data)
    {
    }
}
