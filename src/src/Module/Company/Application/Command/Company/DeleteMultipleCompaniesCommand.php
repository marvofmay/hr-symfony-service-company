<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Company;

use App\Common\Domain\Interface\CommandInterface;

class DeleteMultipleCompaniesCommand implements CommandInterface
{
    public function __construct(public array $selectedUUIDs)
    {
    }
}
