<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\ContractType;

use App\Module\Company\Structure\Validator\Constraints\ContractType\ExistingContractTypeUUID;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['name']
)]
class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'contractType.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'contractType.delete.invalidUUID'),
        new ExistingContractTypeUUID(
            message: ['uuidNotExists' => 'contractType.uuid.notExists', 'domain' => 'contractTypes'],
        ),
    ])]
    public array $selectedUUID = [];

    public function getSelectedUUID(): array
    {
        return $this->selectedUUID;
    }
}
