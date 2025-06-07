<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Interface\Employee;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\User;
use Doctrine\Common\Collections\Collection;

interface EmployeeReaderInterface
{
    public function getEmployeeByUUID(string $uuid): ?Employee;
    public function getEmployeesByUUID(array $selectedUUID): Collection;
    public function getEmployeeByEmail(string $email, ?string $uuid = null): ?User;
    public function isEmployeeWithUUIDExists(string $uuid): bool;
    public function isEmployeeWithEmailExists(string $email, ?string $uuid = null): bool;
}
