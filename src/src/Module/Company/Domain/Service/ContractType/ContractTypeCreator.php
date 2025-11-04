<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Application\Command\ContractType\CreateContractTypeCommand;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use App\Module\System\Domain\Factory\CommandDataMapperFactory;

readonly class ContractTypeCreator
{
    public function __construct(
        private ContractTypeWriterInterface $contractTypeWriterRepository,
        private CommandDataMapperFactory $commandDataMapperFactory,
    )
    {
    }

    public function create(CreateContractTypeCommand $command): void
    {
        $contractType = new ContractType();
        $mapper = $this->commandDataMapperFactory->getMapper(CommandDataMapperKindEnum::COMMAND_MAPPER_CONTRACT_TYPE);
        $mapper->map($contractType, $command);

        $this->contractTypeWriterRepository->saveContractTypeInDB($contractType);
    }
}
