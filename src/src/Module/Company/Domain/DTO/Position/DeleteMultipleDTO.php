<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Position;

use App\Module\Company\Structure\Validator\Constraints\Position\ExistingPositionUUID;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'position.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'position.delete.invalidUUID'),
        new ExistingPositionUUID(
            message: ['uuidNotExists' => 'position.uuid.notExists', 'domain' => 'positions'],
        ),
    ])]
    public array $selectedUUID = [];

    public function getSelectedUUID(): array
    {
        return $this->selectedUUID;
    }
}
