<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Employee;

use App\Common\Domain\Interface\CommandInterface;

final readonly class DeleteMultipleEmployeesCommand implements CommandInterface
{
    public function __construct(public array $selectedUUIDs)
    {
    }
}
