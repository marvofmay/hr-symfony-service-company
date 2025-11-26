<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Service;

use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NotePriorityEnum;
use App\Module\Note\Domain\Interface\NoteCreatorInterface;
use App\Module\Note\Domain\Interface\NoteWriterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class NoteCreator implements NoteCreatorInterface
{
    public function __construct(private NoteWriterInterface $noteWriterRepository)
    {
    }

    public function create(UserInterface $user, string $title, ?string $content = null, NotePriorityEnum $priority = NotePriorityEnum::LOW): void
    {
        $note = Note::create($user, $title, $content, $priority);

        $this->noteWriterRepository->save($note);
    }
}
