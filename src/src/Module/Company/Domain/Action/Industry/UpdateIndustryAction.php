<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Action\Industry;

use App\Module\Company\Application\Command\Industry\UpdateIndustryCommand;
use App\Module\Company\Domain\DTO\Industry\UpdateDTO;
use App\Module\Company\Domain\Entity\Industry;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateIndustryAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private Industry $role)
    {
    }

    public function setIndustryToUpdate(Industry $role): void
    {
        $this->role = $role;
    }

    public function getIndustry(): Industry
    {
        return $this->role;
    }

    public function execute(UpdateDTO $updateDTO): void
    {
        $this->commandBus->dispatch(
            new UpdateIndustryCommand(
                $updateDTO->getUUID(),
                $updateDTO->getName(),
                $updateDTO->getDescription(),
                $this->getIndustry()
            )
        );
    }
}
