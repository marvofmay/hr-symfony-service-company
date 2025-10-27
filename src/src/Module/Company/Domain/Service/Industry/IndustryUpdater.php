<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Industry;

use App\Module\Company\Application\Command\Industry\UpdateIndustryCommand;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Domain\Interface\Industry\IndustryWriterInterface;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use App\Module\System\Domain\Factory\CommandDataMapperFactory;

readonly class IndustryUpdater
{
    public function __construct(
        private IndustryReaderInterface $industryReaderRepository,
        private IndustryWriterInterface $industryWriterRepository,
        private CommandDataMapperFactory $commandDataMapperFactory,
    )
    {
    }

    public function update(UpdateIndustryCommand $command): void
    {
        $industry = $this->industryReaderRepository->getIndustryByUUID($command->industryUUID);
        $mapper = $this->commandDataMapperFactory->getMapper(CommandDataMapperKindEnum::COMMAND_MAPPER_INDUSTRY);
        $mapper->map($industry, $command);
        $this->industryWriterRepository->saveIndustryInDB($industry);
    }
}
