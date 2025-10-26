<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Company;

use App\Common\Domain\Interface\CommandInterface;

final readonly class DeleteMultipleCompaniesCommand implements CommandInterface
{
    public const string COMPANIES_UUIDS = 'companiesUUIDs';

    public function __construct(public array $companiesUUIDs)
    {
    }
}
