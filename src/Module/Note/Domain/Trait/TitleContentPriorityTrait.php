<?php

namespace App\Module\Note\Domain\Trait;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Note\Domain\Enum\NotePriorityEnum;
use Symfony\Component\Validator\Constraints as Assert;

trait TitleContentPriorityTrait
{
    #[NotBlank(message: [
        'text' => 'note.title.required',
        'domain' => 'notes',
    ])]
    #[MinMaxLength(min: 3, max: 100, message: [
        'tooShort' => 'note.title.minimumLength',
        'tooLong' => 'note.title.maximumLength',
        'domain' => 'notes',
    ])]
    public string $title = '' {
        get {
            return $this->title;
        }
    }

    #[Assert\Type('string', message: 'validator.invalidType')]
    public ?string $content = null {
        get {
            return $this->content;
        }
    }

    #[Assert\Type(
        type: NotePriorityEnum::class,
        message: 'note.priority.invalidType',
    )]
    #[NotBlank(message: [
        'text' => 'note.priority.required',
        'domain' => 'notes',
    ])]
    public NotePriorityEnum $priority = NotePriorityEnum::LOW {
        get {
            return $this->priority;
        }
    }
}
