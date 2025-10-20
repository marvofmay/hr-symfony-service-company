<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Company;

use App\Common\Domain\Interface\CommandInterface;

final readonly class RestoreCompanyCommand implements CommandInterface
{
    public function __construct(public string $companyUUID)
    {
    }
}
