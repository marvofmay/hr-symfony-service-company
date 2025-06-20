<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Industry;

use Symfony\Component\Validator\Constraints as Assert;

final class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'industry.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'industry.delete.invalidUUID'),
    ])]
    public array $selectedUUID = [] {
        get {
            return $this->selectedUUID;
        }
    }
}
