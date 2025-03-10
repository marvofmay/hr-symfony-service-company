<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Employee;

use App\Module\System\Application\Command\Import\CreateImportCommand;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\ImportKindEnum;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateImportAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(ImportKindEnum $kindEnum, ImportStatusEnum $statusEnum, ?Employee $employee, ?File $file): void
    {
        $import = new Import();
        $import->setKind($kindEnum);

        match ($statusEnum) {
            ImportStatusEnum::PENDING => $import->markAsPending(),
            ImportStatusEnum::FAILED => $import->markAsFailed(),
            ImportStatusEnum::DONE => $import->markAsDone(),
        };

        $import->setEmployee($employee);
        $import->setFile($file);

        $this->commandBus->dispatch(new CreateImportCommand($import));
    }
}
