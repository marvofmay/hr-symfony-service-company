<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\UpdateIndustryCommand;
use App\Module\Company\Domain\Service\Industry\IndustryUpdater;

readonly class UpdateIndustryCommandHandler
{
    public function __construct(private IndustryUpdater $industryUpdater,)
    {
    }

    public function __invoke(UpdateIndustryCommand $command): void
    {
        $this->industryUpdater->update(
            $command->getIndustry(),
            $command->getName(),
            $command->getDescription()
        );
    }
}
