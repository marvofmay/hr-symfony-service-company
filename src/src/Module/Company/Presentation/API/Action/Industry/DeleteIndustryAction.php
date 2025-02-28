<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Industry;

use App\Module\Company\Application\Command\Industry\DeleteIndustryCommand;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteIndustryAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private readonly IndustryReaderInterface $roleReaderRepository,)
    {
    }

    public function execute(string $uuid): void
    {
        $this->commandBus->dispatch(new DeleteIndustryCommand($this->roleReaderRepository->getIndustryByUUID($uuid)));
    }
}
