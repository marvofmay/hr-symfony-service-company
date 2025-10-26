<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Position;

use Symfony\Component\Validator\Constraints as Assert;

final class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'position.delete.multiple.selectedUUIDsRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'position.delete.invalidUUID'),
    ])]
    public array $positionsUUIDs = [] {
        get {
            return $this->positionsUUIDs;
        }
    }
}
