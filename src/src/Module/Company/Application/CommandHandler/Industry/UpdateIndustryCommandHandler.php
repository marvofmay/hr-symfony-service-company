<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\UpdateIndustryCommand;
use App\Module\Company\Domain\Service\Industry\IndustryService;

readonly class UpdateIndustryCommandHandler
{
    public function __construct(private IndustryService $industryWriterService)
    {
    }

    public function __invoke(UpdateIndustryCommand $command): void
    {
        $industry = $command->getIndustry();
        $industry->setName($command->getName());
        $industry->setDescription($command->getDescription());
        $industry->setUpdatedAt(new \DateTime());

        $this->industryWriterService->updateIndustryInDB($industry);
    }
}
