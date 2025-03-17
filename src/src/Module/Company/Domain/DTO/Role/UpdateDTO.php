<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Module\Company\Structure\Validator\Constraints\Role\ExistingRoleUUID;

#[OA\Schema(
    required: ['uuid']
)]
class UpdateDTO extends CreateDTO
{
    #[OA\Property(
        description: 'UUID aktualizowanej roli',
        type: 'string',
        example: '1343b681-39ea-4917-ae2f-7a9296690116',
    )]
    #[ExistingRoleUUID(
        message: ['uuidNotExists' => 'role.uuid.notExists', 'domain' => 'roles']
    )]
    #[Assert\NotBlank()]
    public string $uuid = '';

    public function getUUID(): string
    {
        return $this->uuid;
    }
}
