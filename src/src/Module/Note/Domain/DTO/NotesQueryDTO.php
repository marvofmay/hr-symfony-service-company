<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\DTO;

use App\Common\Domain\Abstract\QueryDTOAbstract;
use App\Module\Note\Domain\Enum\NotePriorityEnum;

class NotesQueryDTO extends QueryDTOAbstract
{
    #[OA\Property(description: 'Nazwa notatki', type: 'string', nullable: true)]
    public ?string $title = null;

    #[OA\Property(description: 'Opis notatki', type: 'string', nullable: true)]
    public ?string $content = null;

    #[OA\Property(description: 'Priorytet notatki', type: 'string', default: NotePriorityEnum::HIGH, enum: [NotePriorityEnum::LOW, NotePriorityEnum::MEDIUM, NotePriorityEnum::HIGH], nullable: true)]
    public ?NotePriorityEnum $priority;
}
