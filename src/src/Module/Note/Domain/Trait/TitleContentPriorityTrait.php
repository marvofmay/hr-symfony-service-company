<?php

namespace App\Module\Note\Domain\Trait;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Note\Domain\Enum\NotePriorityEnum;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

trait TitleContentPriorityTrait
{
    #[OA\Property(
        description: 'Tytuł tworzonej notatki',
        type: 'string',
        maxLength: 100,
        minLength: 3,
        example: 'Zadzwoń!!!',
        nullable: true
    )]
    #[NotBlank(message: [
        'text' => 'note.title.required',
        'domain' => 'notes',
    ])]
    #[MinMaxLength(min: 3, max: 100, message: [
        'tooShort' => 'note.title.minimumLength',
        'tooLong' => 'note.title.maximumLength',
        'domain' => 'notes',
    ])]
    public string $title = '';

    #[OA\Property(
        description: 'Treść tworzonej notatki',
        type: 'string',
        example: 'Koniecznie w piątek, zadzwoń do przełożonego.',
        nullable: true
    )]
    #[Assert\Type('string', message: 'validator.invalidType')]
    public ?string $content = null;

    #[OA\Property(
        description: 'Priorytet tworzonej notatki (możliwe wartości: low, medium, high)',
        type: 'string',
        enum: [NotePriorityEnum::LOW, NotePriorityEnum::MEDIUM, NotePriorityEnum::HIGH],
        example: NotePriorityEnum::HIGH,
        nullable: false
    )]
    #[NotBlank(message: [
        'text' => 'note.priority.required',
        'domain' => 'notes',
    ])]
    public ?NotePriorityEnum $priority = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getPriority(): NotePriorityEnum
    {
        return $this->priority;
    }
}