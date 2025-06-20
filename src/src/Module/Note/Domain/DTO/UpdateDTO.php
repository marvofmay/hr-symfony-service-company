<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\DTO;

use App\Module\Note\Domain\Trait\TitleContentPriorityTrait;
use App\Module\Note\Structure\Validator\Constraints\ExistingNoteUUID;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDTO
{
    use TitleContentPriorityTrait;

    #[Assert\NotBlank()]
    #[ExistingNoteUUID(
        message: ['uuidNotExists' => 'note.uuid.notExists', 'domain' => 'notes']
    )]
    public string $uuid = '';

    public function getUUID(): string
    {
        return $this->uuid;
    }
}
