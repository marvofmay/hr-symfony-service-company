<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Application\Command\ContractType\UpdateContractTypeCommand;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeUpdaterInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use App\Module\System\Domain\Factory\CommandDataMapperFactory;

final readonly class ContractTypeUpdater implements ContractTypeUpdaterInterface
{
    public function __construct(
        private ContractTypeReaderInterface $contractTypeReaderRepository,
        private ContractTypeWriterInterface $contractTypeWriterRepository,
        private CommandDataMapperFactory $commandDataMapperFactory,
    )
    {
    }

    public function update(UpdateContractTypeCommand $command): void
    {
        $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($command->contractTypeUUID);

        $mapper = $this->commandDataMapperFactory->getMapper(CommandDataMapperKindEnum::COMMAND_MAPPER_CONTRACT_TYPE);
        $mapper->map($contractType, $command);

        $this->contractTypeWriterRepository->saveContractTypeInDB($contractType);
    }
}
