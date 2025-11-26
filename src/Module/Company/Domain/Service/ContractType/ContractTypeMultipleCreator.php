<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Enum\ContractType\ContractTypeImportColumnEnum;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;
use Doctrine\Common\Collections\ArrayCollection;

readonly class ContractTypeMultipleCreator
{
    public function __construct(private ContractTypeWriterInterface $contractTypeWriterRepository)
    {
    }

    public function multipleCreate(array $data): void
    {
        $contractTypes = new ArrayCollection();
        foreach ($data as $item) {
            $contractType = ContractType::create(
                $item[ContractTypeImportColumnEnum::CONTRACT_TYPE_NAME->value],
                $item[ContractTypeImportColumnEnum::CONTRACT_TYPE_DESCRIPTION->value],
                (bool)$item[ContractTypeImportColumnEnum::CONTRACT_TYPE_ACTIVE->value]
            );

            $contractTypes[] = $contractType;
        }

        $this->contractTypeWriterRepository->saveContractTypesInDB($contractTypes);
    }
}
