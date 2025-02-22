<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\DTO;

use App\Common\Validator\Constraints\MinMaxLength;
use App\Common\Validator\Constraints\NotBlank;
use App\Module\Note\Domain\Enum\NotePriorityEnum;

class CreateDTO
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

    public ?string $content = null;

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
