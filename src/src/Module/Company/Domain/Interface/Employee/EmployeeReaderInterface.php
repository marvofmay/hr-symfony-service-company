<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Employee;

use App\Module\Company\Domain\Entity\Employee;
use Doctrine\Common\Collections\Collection;

interface EmployeeReaderInterface
{
    public function getEmployeeByUUID(string $uuid): ?Employee;
    public function getEmployeesByUUID(array $selectedUUID): Collection;
}
