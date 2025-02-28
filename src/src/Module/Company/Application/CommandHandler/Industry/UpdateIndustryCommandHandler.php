<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\Industry;

use App\Module\Company\Application\Command\Industry\UpdateIndustryCommand;
use App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Industry\Writer\IndustryWriterRepository;

readonly class UpdateIndustryCommandHandler
{
    public function __construct(private IndustryWriterRepository $industryWriterRepository)
    {
    }

    public function __invoke(UpdateIndustryCommand $command): void
    {
        $industry = $command->getIndustry();
        $industry->setName($command->getName());
        $industry->setDescription($command->getDescription());
        $industry->setUpdatedAt(new \DateTime());

        $this->industryWriterRepository->updateIndustryInDB($industry);
    }
}
