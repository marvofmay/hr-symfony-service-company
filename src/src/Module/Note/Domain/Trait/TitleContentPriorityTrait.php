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
    public string $title = '';

    #[Assert\Type('string', message: 'validator.invalidType')]
    public ?string $content = null;

    // ToDo add custom validator - value on of ENUM
    public NotePriorityEnum $priority = NotePriorityEnum::LOW;

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