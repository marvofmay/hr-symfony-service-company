<?php

declare(strict_types=1);

namespace App\Module\Company\Application\DTO\ContractType;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'contractType.delete.multiple.selectedUUIDsRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'contractType.delete.invalidUUID'),
    ])]
    public array $contractTypesUUIDs = [];

    public function getSelectedUUID(): array
    {
        return $this->contractTypesUUIDs;
    }
}
