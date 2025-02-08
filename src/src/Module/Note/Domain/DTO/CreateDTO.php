<?php

declare(strict_types = 1);

namespace App\Module\Note\Domain\DTO;

use App\Module\Note\Domain\Enum\NotePriorityEnum;
use Symfony\Component\Validator\Constraints as Assert;

class CreateDTO
{
    #[Assert\NotBlank(message: "note.title.required")]
    #[Assert\Length(min: 3, max: 150, minMessage: 'note.title.minimum150Letters', maxMessage: 'note.title.maximum150Letters')]
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