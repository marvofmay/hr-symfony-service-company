<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Company;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'company.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'company.delete.invalidUUID'),
    ])]
    public array $selectedUUIDs = [] {
        get {
            return $this->selectedUUIDs;
        }
    }
}
