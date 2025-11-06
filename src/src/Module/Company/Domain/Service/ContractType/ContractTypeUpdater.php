<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeUpdaterInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;

final readonly class ContractTypeUpdater implements ContractTypeUpdaterInterface
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository)
    {
    }

    public function update(ContractType $contractType, string $name, ?string $description, bool $active = false): void
    {
        $contractType->rename($name);
        if (null !== $description) {
            $contractType->updateDescription($description);
        }
        if ($active) {
            $contractType->activate();
        } else {
            $contractType->deactivate();
        }

        $this->contractTypeWriterRepository->saveContractTypeInDB($contractType);
    }
}
