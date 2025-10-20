<?php

declare(strict_types=1);

namespace App\Module\System\Application\CommandHandler\Import;

use App\Module\System\Application\Command\Import\UpdateImportCommand;
use App\Module\System\Domain\Service\Import\ImportUpdater;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class UpdateImportCommandHandler
{
    public function __construct(private ImportUpdater $importUpdater)
    {
    }

    public function __invoke(UpdateImportCommand $command): void
    {
        $this->importUpdater->update($command->import, $command->importStatusEnum);
    }
}
