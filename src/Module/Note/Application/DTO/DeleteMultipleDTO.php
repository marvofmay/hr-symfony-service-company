<?php

declare(strict_types=1);

namespace App\Module\Note\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'note.delete.multiple.selectedUUIDsRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'note.delete.invalidUUID'),
    ])]
    public array $notesUUIDs = [] {
        get {
            return $this->notesUUIDs;
        }
    }
}
