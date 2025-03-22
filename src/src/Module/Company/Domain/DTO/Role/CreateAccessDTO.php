<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use App\Common\Validator\Constraints\NotBlank;
use App\Module\System\Structure\Validator\Constraints\Access\ExistingAccessUUID;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Module\Company\Structure\Validator\Constraints\Role\ExistingRoleUUID;;

#[OA\Schema(
    required: ['uuid']
)]
class CreateAccessDTO
{
    #[OA\Property(
        description: 'UUID roli',
        type: 'string',
        example: '1343b681-39ea-4917-ae2f-7a9296690116',
    )]
    #[ExistingRoleUUID(
        message: ['uuidNotExists' => 'role.uuid.notExists', 'domain' => 'roles']
    )]
    #[NotBlank(message: [
        'text' => 'role.uuid.required',
        'domain' => 'roles',
    ])]
    public string $roleUUID = '';

    #[NotBlank(message: [
        'text' => 'access.uuid.required',
        'domain' => 'accesses',
    ])]
    #[Assert\All([
        new Assert\Uuid(message: 'uuid.invalid'),
        new ExistingAccessUUID(message: ['uuidNotExists' => 'access.notExists', 'domain' => 'accesses']),
    ])]
    public array $accessUUID = [];

    public function getRoleUUID(): string
    {
        return $this->roleUUID;
    }

    public function getAccessUUID(): array
    {
        return $this->accessUUID;
    }
}
