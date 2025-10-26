<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Application\Command\Industry\CreateIndustryCommand;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use App\Module\System\Domain\Factory\CommandDataMapperFactory;

readonly class IndustryCreator
{
    public function __construct(
        private IndustryWriterInterface $industryWriterRepository,
        private CommandDataMapperFactory $commandDataMapperFactory,
    )
    {
    }

    public function create(CreateIndustryCommand $command): void
    {
        $industry = new Industry();
        $mapper = $this->commandDataMapperFactory->getMapper(CommandDataMapperKindEnum::COMMAND_MAPPER_INDUSTRY);
        $mapper->map($industry, $command);
        $this->industryWriterRepository->saveIndustryInDB($industry);
    }
}
