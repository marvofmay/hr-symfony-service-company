<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Service;

use App\Module\Note\Domain\Interface\NoteMultipleDeleterInterface;
use App\Module\Note\Domain\Interface\NoteWriterInterface;
use Doctrine\Common\Collections\Collection;

readonly class NoteMultipleDeleter implements NoteMultipleDeleterInterface
{
    public function __construct(private NoteWriterInterface $noteWriterRepository)
    {
    }

    public function multipleDelete(Collection $notes): void
    {
        $this->noteWriterRepository->deleteMultipleNotes($notes);
    }
}
