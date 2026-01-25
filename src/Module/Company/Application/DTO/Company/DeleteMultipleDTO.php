<?php

declare(strict_types=1);

namespace App\Module\Company\Application\DTO\Company;

use Symfony\Component\Validator\Constraints as Assert;

class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'company.delete.multiple.selectedUUIDsRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'company.delete.invalidUUID'),
    ])]
    public array $companiesUUIDs = [] {
        get {
            return $this->companiesUUIDs;
        }
    }
}
