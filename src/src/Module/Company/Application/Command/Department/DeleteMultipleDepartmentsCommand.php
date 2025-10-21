<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Department;

use App\Common\Domain\Interface\CommandInterface;

final readonly class DeleteMultipleDepartmentsCommand implements CommandInterface
{
    public function __construct(public array $selectedUUIDs)
    {
    }
}
