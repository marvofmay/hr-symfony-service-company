<?php

declare(strict_types=1);

namespace App\Module\Note\Application\CommandHandler;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Note\Application\Command\CreateNoteCommand;
use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Interface\NoteWriterInterface;

readonly class CreateNoteCommandHandler
{
    private CreateNoteCommand $command;

    public function __construct(
        private Note $note,
        private NoteWriterInterface $noteWriterRepository,
        private EmployeeReaderInterface $employeeReaderRepository
    ) {}

    public function __invoke(CreateNoteCommand $command): void
    {
        $this->command = $command;

        $this->note->setEmployee($this->getEmployee());
        $this->note->setTitle($this->command->title);
        $this->note->setContent($this->command->content);
        $this->note->setPriority($this->command->priority);

        $this->noteWriterRepository->saveNoteInDB($this->note);
    }

    private function getEmployee(): Employee
    {
        return $this->employeeReaderRepository->getEmployeeByUUID($this->command->employeeUUID);
    }
}