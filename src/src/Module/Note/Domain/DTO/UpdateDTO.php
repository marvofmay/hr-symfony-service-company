<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\DTO;

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
