<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\DTO;

use App\Common\Validator\Constraints\NotBlank;
use App\Module\Note\Domain\Trait\TitleContentPriorityTrait;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['priority']
)]
class CreateDTO
{
    use TitleContentPriorityTrait;

    #[NotBlank(message: [
        'text' => 'note.employeeUUID.required',
        'domain' => 'notes',
    ])]
    public string $employeeUUID;

    public function getEmployeeUUID(): string
    {
        return $this->employeeUUID;
    }
}
