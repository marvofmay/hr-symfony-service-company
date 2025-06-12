<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Position;

use App\Module\Company\Structure\Validator\Constraints\Position\ExistingPositionUUID;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDTO extends CreateDTO
{
    #[ExistingPositionUUID(
        message: ['uuidNotExists' => 'position.uuid.notExists', 'domain' => 'positions']
    )]
    #[Assert\NotBlank]
    public string $uuid = '';

    public function getUUID(): string
    {
        return $this->uuid;
    }
}
