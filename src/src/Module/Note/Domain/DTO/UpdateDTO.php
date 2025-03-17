<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Module\Note\Domain\Trait\TitleContentPriorityTrait;
use App\Module\Note\Structure\Validator\Constraints\ExistingNoteUUID;

#[OA\Schema(
    required: ['priority']
)]
class UpdateDTO
{
    use TitleContentPriorityTrait;

    #[OA\Property(
        description: 'UUID aktualizowanej notatki',
        type: 'string',
        example: '1343b681-39ea-4917-ae2f-7a9296690116',
    )]
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
