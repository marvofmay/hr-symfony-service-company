<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Position;

use Symfony\Component\Validator\Constraints as Assert;

final class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'position.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'position.delete.invalidUUID'),
    ])]
    public array $selectedUUID = [] {
        get {
            return $this->selectedUUID;
        }
    }
}
