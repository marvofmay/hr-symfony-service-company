<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Interface;

use App\Module\Note\Domain\Enum\NotePriorityEnum;
use Symfony\Component\Security\Core\User\UserInterface;

interface NoteCreatorInterface
{
    public function create(UserInterface $user, string $title, ?string $content = null, NotePriorityEnum $priority = NotePriorityEnum::LOW): void;
}