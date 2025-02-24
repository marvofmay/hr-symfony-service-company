<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Action\Industry;

use App\Module\Company\Application\Command\Industry\CreateIndustryCommand;
use App\Module\Company\Domain\DTO\Industry\CreateDTO;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateIndustryAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function execute(CreateDTO $createDTO): void
    {
        $this->commandBus->dispatch(
            new CreateIndustryCommand(
                $createDTO->getName(),
                $createDTO->getDescription()
            )
        );
    }
}
