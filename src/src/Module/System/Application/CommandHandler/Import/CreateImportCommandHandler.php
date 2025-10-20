<?php

declare(strict_types=1);

namespace App\Module\System\Application\CommandHandler\Import;

use App\Module\System\Application\Command\Import\CreateImportCommand;
use App\Module\System\Domain\Service\Import\ImportCreator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateImportCommandHandler
{
    public function __construct(private ImportCreator $importCreator)
    {
    }

    public function __invoke(CreateImportCommand $command): void
    {
        $this->importCreator->create($command->kindEnum, $command->statusEnum, $command->file, $command->employee);
    }
}
