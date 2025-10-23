<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Command\Employee;

use App\Common\Domain\Interface\CommandInterface;

final readonly class RestoreEmployeeCommand implements CommandInterface
{
    public function __construct(public string $employeeUUID)
    {
    }
}
