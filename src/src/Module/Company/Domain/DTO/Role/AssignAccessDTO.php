<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Role;

use Symfony\Component\Validator\Constraints as Assert;

class AssignAccessDTO
{
    #[Assert\All([
        new Assert\Uuid(message: 'uuid.invalid'),
    ])]
    public array $accessesUUIDs = [] {
        get {
            return $this->accessesUUIDs;
        }
    }
}
