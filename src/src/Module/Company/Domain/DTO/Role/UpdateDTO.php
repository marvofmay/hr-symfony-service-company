<?php

declare(strict_types = 1);

namespace App\Module\Company\Domain\DTO\Role;

use App\Module\Company\Domain\DTO\Role\CreateDTO;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDTO extends CreateDTO
{
    #[Assert\NotBlank()]
    public string $uuid = '';

    public function getUuid(): string
    {
        return $this->uuid;
    }
}