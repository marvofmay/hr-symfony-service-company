<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Company;

use App\Module\Company\Structure\Validator\Constraints\Company\ExistingCompanyUUID;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['uuid']
)]
class UpdateDTO extends CreateDTO
{
    #[OA\Property(
        description: 'UUID aktualizowanej firmy',
        type: 'string',
        example: '1343b681-39ea-4917-ae2f-7a9296690116',
    )]
    #[ExistingCompanyUUID(
        message: ['uuidNotExists' => 'company.uuid.notExists', 'domain' => 'companies']
    )]
    #[Assert\NotBlank()]
    public string $uuid;

    public function getUUID(): string
    {
        return $this->uuid;
    }
}
