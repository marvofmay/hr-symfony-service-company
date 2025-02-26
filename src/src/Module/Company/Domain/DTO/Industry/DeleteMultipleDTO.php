<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\DTO\Industry;

use App\Module\Company\Structure\Validator\Constraints\Industry\ExistingIndustryUUID;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'industry.delete.selectedRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'industry.delete.invalidUUID'),
        new ExistingIndustryUUID(
            message: ['uuidNotExists' => 'industry.uuid.notExists', 'domain' => 'industries'],
        ),
    ])]
    public array $selectedUUID = [];

    public function getSelectedUUID(): array
    {
        return $this->selectedUUID;
    }
}
