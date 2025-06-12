<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Department;

use App\Module\Company\Structure\Validator\Constraints\Department\ExistingDepartmentUUID;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDTO extends CreateDTO
{
    #[ExistingDepartmentUUID(
        message: ['uuidNotExists' => 'department.uuid.notExists', 'domain' => 'departments']
    )]
    #[Assert\NotBlank]
    public string $uuid;

    public function getUUID(): string
    {
        return $this->uuid;
    }
}
