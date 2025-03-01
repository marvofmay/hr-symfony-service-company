<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\ContractType;

use App\Module\Company\Structure\Validator\Constraints\ContractType\ExistingContractTypeUUID;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    required: ['uuid']
)]
class UpdateDTO extends CreateDTO
{
    #[OA\Property(
        description: 'UUID formy zatrudnienia',
        type: 'string',
        example: '1343b681-39ea-4917-ae2f-7a9296690116',
    )]
    #[ExistingContractTypeUUID(
        message: ['uuidNotExists' => 'contractType.uuid.notExists', 'domain' => 'contract_types']
    )]
    #[Assert\NotBlank()]
    public string $uuid = '';

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
