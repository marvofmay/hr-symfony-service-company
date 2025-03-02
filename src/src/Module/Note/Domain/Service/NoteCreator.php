<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Service;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Note\Application\Command\CreateNoteCommand;
use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Enum\NotePriorityEnum;
use App\Module\Note\Domain\Interface\NoteWriterInterface;

readonly class NoteCreator
{
    public function __construct(private NoteWriterInterface $noteWriterRepository, private EmployeeReaderInterface $employeeReaderRepository,)
    {
    }

    public function create(CreateNoteCommand $command): void
    {
        $note = new Note();
        $note->setEmployee($this->employeeReaderRepository->getEmployeeByUUID($command->employeeUUID));
        $note->setTitle($command->title);
        $note->setContent($command->content);
        $note->setPriority($command->priority);

        $this->noteWriterRepository->saveNoteInDB($note);
    }
}