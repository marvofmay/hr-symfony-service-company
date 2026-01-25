<?php

declare(strict_types=1);

namespace App\Module\Company\Application\DTO\Industry;

use Symfony\Component\Validator\Constraints as Assert;

final class DeleteMultipleDTO
{
    #[Assert\NotBlank(message: 'industry.delete.multiple.selectedUUIDsRequired')]
    #[Assert\All([
        new Assert\Uuid(message: 'industry.delete.invalidUUID'),
    ])]
    public array $industriesUUIDs = [] {
        get {
            return $this->industriesUUIDs;
        }
    }
}
