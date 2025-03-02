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

    #[OA\Property(
        description: 'UUID pracownika',
        type: 'string',
        format: 'uuid',
        example: '550e8400-e29b-41d4-a716-446655440000',
        nullable: false
    )]
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
