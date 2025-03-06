<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Employee;

use App\Module\Company\Structure\Validator\Constraints\Employee\ExistingEmployeeUUID;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['uuid']
)]
class UpdateDTO extends CreateDTO
{
    #[OA\Property(
        description: 'UUID aktualizowanego pracownika',
        type: 'string',
        example: '1343b681-39ea-4917-ae2f-7a9296690116',
    )]
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
