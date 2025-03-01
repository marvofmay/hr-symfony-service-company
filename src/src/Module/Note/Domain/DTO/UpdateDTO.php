<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\DTO;

use App\Module\Note\Domain\Trait\TitleContentPriorityTrait;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDTO
{
    use TitleContentPriorityTrait;

    #[Assert\NotBlank()]
    // ToDo add existsUUIDNote validator
    public string $uuid = '';

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
