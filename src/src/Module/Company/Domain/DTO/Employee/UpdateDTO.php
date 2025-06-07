<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Employee;

use App\Module\Company\Structure\Validator\Constraints\Employee\ExistingEmployeeUUID;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDTO extends CreateDTO
{
    #[ExistingEmployeeUUID(
        message: ['uuidNotExists' => 'employee.uuid.notExists', 'domain' => 'employees']
    )]
    #[Assert\NotBlank()]
    public string $uuid;

    public function getUUID(): string
    {
        return $this->uuid;
    }
}
