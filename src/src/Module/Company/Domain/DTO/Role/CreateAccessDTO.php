<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use App\Common\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class CreateAccessDTO
{
    #[NotBlank(message: [
        'text' => 'access.uuid.required',
        'domain' => 'accesses',
    ])]
    #[Assert\All([
        new Assert\Uuid(message: 'uuid.invalid'),
    ])]
    public array $accessUUID = [] {
        get {
            return $this->accessUUID;
        }
    }
}
