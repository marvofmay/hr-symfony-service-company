<?php

declare(strict_types=1);

namespace App\Module\System\Presentation\API\Action\Import;

use App\Module\System\Application\Command\Import\UpdateImportCommand;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\ImportStatusEnum;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class UpdateImportAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(Import $import, ImportStatusEnum $statusEnum): void
    {
        $this->commandBus->dispatch(new UpdateImportCommand($import, $statusEnum));
    }
}