<?php

declare(strict_types=1);

namespace App\Module\Note\Domain\Service;

use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use App\Module\Note\Application\Command\CreateNoteCommand;
use App\Module\Note\Domain\Entity\Note;
use App\Module\Note\Domain\Interface\NoteWriterInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class NoteCreator
{
    public function __construct(private NoteWriterInterface $noteWriterRepository, private Security $security,)
    {
    }

    public function create(CreateNoteCommand $command): void
    {
        $employee = $this->security->getUser()->getEmployee();

        $note = new Note();
        $note->setEmployee($employee);
        $note->setTitle($command->title);
        $note->setContent($command->content);
        $note->setPriority($command->priority);

        $this->noteWriterRepository->saveOrUpdateNoteInDB($note);
    }
}