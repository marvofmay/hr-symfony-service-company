<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Company;

class ImportCompaniesCommand
{
    public function __construct(public array $data)
    {
    }
}
