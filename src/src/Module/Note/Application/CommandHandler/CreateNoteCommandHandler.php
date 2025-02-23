<?php

declare(strict_types=1);

namespace App\Module\Note\Application\CommandHandler;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Note\Application\Command\CreateNoteCommand;
use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Service\NoteService;

readonly class CreateNoteCommandHandler
{
    private CreateNoteCommand $command;

    public function __construct(
        private NoteService $noteService,
        private EmployeeReaderInterface $employeeReaderRepository
    ) {}

    public function __invoke(CreateNoteCommand $command): void
    {
        $this->command = $command;

        $note = new Note();
        $note->setEmployee($this->getEmployee());
        $note->setTitle($this->command->title);
        $note->setContent($this->command->content);
        $note->setPriority($this->command->priority);

        $this->noteService->saveNoteInDB($note);
    }

    private function getEmployee(): Employee
    {
        return $this->employeeReaderRepository->getEmployeeByUUID($this->command->employeeUUID);
    }
}