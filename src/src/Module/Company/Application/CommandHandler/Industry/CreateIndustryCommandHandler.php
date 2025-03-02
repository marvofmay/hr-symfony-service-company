<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\CreateIndustryCommand;
use App\Module\Company\Domain\Service\Industry\IndustryCreator;

readonly class CreateIndustryCommandHandler
{
    public function __construct(private IndustryCreator $industryCreator)
    {
    }

    public function __invoke(CreateIndustryCommand $command): void
    {
        $this->industryCreator->create($command->name, $command->description);
    }
}
