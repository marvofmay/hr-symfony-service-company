<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use App\Common\Validator\Constraints\NotBlank;
use App\Module\System\Structure\Validator\Constraints\Access\ExistingAccessUUID;
use App\Module\System\Structure\Validator\Constraints\Permission\ExistingPermissionUUID;
use Symfony\Component\Validator\Constraints as Assert;

class CreateAccessPermissionDTO
{
    #[NotBlank(message: [
        'text' => 'role.accesses.required',
        'domain' => 'roles',
    ])]
    #[Assert\All([
        new Assert\Collection(
            fields: [
                'uuid' => [
                    new NotBlank(message: ['text' => 'access.uuid.required', 'domain' => 'accesses']),
                    new Assert\Uuid(),
                    new ExistingAccessUUID(message: ['uuidNotExists' => 'access.notExists', 'domain' => 'accesses']),
                ],
                'permissions' => [
                    new NotBlank(message: ['text' => 'permission.uuid.required', 'domain' => 'permissions']),
                    new Assert\All([
                        new Assert\Uuid(),
                        new ExistingPermissionUUID(message: ['uuidNotExists' => 'permission.notExists', 'domain' => 'permissions']),
                    ]),
                ],
            ],
            allowExtraFields: false,
        ),
    ])]
    public array $accesses = [];

    public function getAccesses(): array
    {
        return $this->accesses;
    }
}
