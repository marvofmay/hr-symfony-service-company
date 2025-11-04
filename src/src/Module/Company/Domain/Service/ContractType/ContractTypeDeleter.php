<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Application\Command\ContractType\DeleteContractTypeCommand;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeDeleterInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;

final readonly class ContractTypeDeleter implements ContractTypeDeleterInterface
{
    public function __construct(
        private ContractTypeReaderInterface $contractTypeReaderRepository,
        private ContractTypeWriterInterface $contractTypeWriterRepository,
    )
    {
    }

    public function delete(DeleteContractTypeCommand $command): void
    {
        $contractType = $this->contractTypeReaderRepository->getContractTypeByUUID($command->contractTypeUUID);

        $this->contractTypeWriterRepository->deleteContractTypeInDB($contractType);
    }
}
