<?php

declare(strict_types=1);

namespace App\Module\System\Presentation\API\Action\Import;

use App\Module\System\Application\Command\Import\UpdateImportCommand;
use App\Module\System\Domain\Entity\Import;
use App\Module\System\Domain\Enum\Import\ImportStatusEnum;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class UpdateImportAction
{
    public function __construct(#[Autowire(service: 'command.bus')] private MessageBusInterface $commandBus)
    {
    }

    public function execute(Import $import, ImportStatusEnum $statusEnum): void
    {
        $this->commandBus->dispatch(new UpdateImportCommand($import, $statusEnum));
    }
}
