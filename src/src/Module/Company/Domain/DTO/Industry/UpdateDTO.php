<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Industry;

use App\Module\Company\Structure\Validator\Constraints\Industry\ExistingIndustryUUID;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    required: ['uuid']
)]
class UpdateDTO extends CreateDTO
{
    #[OA\Property(
        description: 'UUID aktualizowanej branży',
        type: 'string',
        example: '1343b681-39ea-4917-ae2f-7a9296690116',
    )]
    #[Assert\NotBlank()]
    #[ExistingIndustryUUID(
        message: ['uuidNotExists' => 'industry.uuid.notExists', 'domain' => 'industries']
    )]
    public string $uuid = '';

    public function getUUID(): string
    {
        return $this->uuid;
    }
}
