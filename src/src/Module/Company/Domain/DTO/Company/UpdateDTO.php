<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Company;

use App\Module\Company\Structure\Validator\Constraints\Company\ExistingCompanyUUID;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDTO extends CreateDTO
{
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
