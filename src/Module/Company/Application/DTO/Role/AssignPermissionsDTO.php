<?php

declare(strict_types=1);

namespace App\Module\Company\Application\DTO\Role;

use App\Common\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class AssignPermissionsDTO
{
    #[NotBlank(message: [
        'text' => 'role.accesses.required',
        'domain' => 'roles',
    ])]
    #[Assert\All([
        new Assert\Collection(
            fields: [
                'accessUUID' => [
                    new NotBlank(message: ['text' => 'access.uuid.required', 'domain' => 'accesses']),
                    new Assert\Uuid(),
                ],
                'permissionsUUIDs' => [
                    new Assert\All([
                        new Assert\Uuid(),
                    ]),
                ],
            ],
            allowExtraFields: false,
        ),
    ])]
    public array $accesses = [] {
        get {
            return $this->accesses;
        }
    }

}
