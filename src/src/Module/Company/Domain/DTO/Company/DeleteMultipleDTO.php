<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Company;

use App\Module\Company\Structure\Validator\Constraints\Company\ExistingCompanyUUID;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'company.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'company.delete.invalidUUID'),
        new ExistingCompanyUUID(
            message: ['uuidNotExists' => 'company.uuid.notExists', 'domain' => 'companies'],
        ),
    ])]
    public array $selectedUUID = [];

    public function getSelectedUUID(): array
    {
        return $this->selectedUUID;
    }
}
