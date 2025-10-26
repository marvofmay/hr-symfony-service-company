<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\ContractType;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'contractType.delete.multiple.selectedUUIDsRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'contractType.delete.invalidUUID'),
    ])]
    public array $selectedUUID = [];

    public function getSelectedUUID(): array
    {
        return $this->selectedUUID;
    }
}
