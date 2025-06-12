<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Industry;

use App\Module\Company\Structure\Validator\Constraints\Industry\ExistingIndustryUUID;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDTO extends CreateDTO
{
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
