<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Company;

use App\Common\Domain\Interface\CommandInterface;

readonly class ImportCompaniesCommand implements CommandInterface
{
    public function __construct(public string $importUUID)
    {
    }
}
