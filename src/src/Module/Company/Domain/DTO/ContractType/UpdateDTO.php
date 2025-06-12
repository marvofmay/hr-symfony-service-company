<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\ContractType;

use App\Module\Company\Structure\Validator\Constraints\ContractType\ExistingContractTypeUUID;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDTO extends CreateDTO
{
    #[ExistingContractTypeUUID(
        message: ['uuidNotExists' => 'contractType.uuid.notExists', 'domain' => 'contract_types']
    )]
    #[Assert\NotBlank()]
    public string $uuid = '';

    public function getUUID(): string
    {
        return $this->uuid;
    }
}
