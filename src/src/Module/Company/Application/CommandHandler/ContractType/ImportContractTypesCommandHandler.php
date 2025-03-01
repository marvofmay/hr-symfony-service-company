<?php

declare(strict_types=1);

namespace App\Module\Company\Application\CommandHandler\ContractType;

use App\Module\Company\Application\Command\ContractType\ImportContractTypesCommand;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;
use App\Module\Company\Domain\Service\ContractType\ImportContractTypesFromXLSX;

readonly class ImportContractTypesCommandHandler
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository,)
    {
    }

    public function __invoke(ImportContractTypesCommand $command): void
    {
        $contractTypes = [];
        foreach ($command->data as $item) {
            $contractType = new ContractType();
            $contractType->setName($item[ImportContractTypesFromXLSX::COLUMN_NAME]);
            $contractType->setDescription($item[ImportContractTypesFromXLSX::COLUMN_DESCRIPTION]);

            $contractTypes[] = $contractType;
        }

        $this->contractTypeWriterRepository->saveContractTypesInDB($contractTypes);
    }
}
