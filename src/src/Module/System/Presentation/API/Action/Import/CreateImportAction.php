<?php

declare(strict_types=1);

namespace App\Module\System\Presentation\API\Action\Import;

use App\Module\Company\Domain\Entity\Employee;
use App\Module\System\Application\Command\Import\CreateImportCommand;
use App\Module\System\Domain\Entity\File;
use App\Module\System\Domain\Enum\Import\ImportKindEnum;
use App\Module\System\Domain\Enum\Import\ImportStatusEnum;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateImportAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(ImportKindEnum $kindEnum, ImportStatusEnum $statusEnum, ?File $file, ?Employee $employee = null): void
    {
        $this->commandBus->dispatch(new CreateImportCommand($kindEnum, $statusEnum, $file, $employee));
    }
}
