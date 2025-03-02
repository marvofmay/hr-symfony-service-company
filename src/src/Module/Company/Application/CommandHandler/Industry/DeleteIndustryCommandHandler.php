<?php

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\DeleteIndustryCommand;
use App\Module\Company\Domain\Service\Industry\IndustryDeleter;

readonly class DeleteIndustryCommandHandler
{
    public function __construct(private IndustryDeleter $industryDeleter)
    {
    }

    public function __invoke(DeleteIndustryCommand $command): void
    {
        $this->industryDeleter->delete($command->getIndustry());
    }
}
