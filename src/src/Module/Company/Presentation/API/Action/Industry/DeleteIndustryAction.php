<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Action\Industry;

use App\Module\Company\Application\Command\Industry\DeleteIndustryCommand;
use App\Module\Company\Domain\Entity\Industry;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteIndustryAction
{
    public function __construct(private readonly MessageBusInterface $commandBus, private Industry $industry)
    {
    }

    public function setIndustryToDelete(Industry $industry): self
    {
        $this->industry = $industry;

        return $this;
    }

    public function execute(): void
    {
        $this->commandBus->dispatch(new DeleteIndustryCommand($this->industry));
    }
}
