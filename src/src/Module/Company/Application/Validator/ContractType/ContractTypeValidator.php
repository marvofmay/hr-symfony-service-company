<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\ContractType;

use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ContractTypeValidator
{
    public function __construct(private ContractTypeReaderInterface $contractTypeReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function isContractTypeNameAlreadyExists(string $name, ?string $uuid = null): void
    {
        if ($this->contractTypeReaderRepository->isContractTypeNameAlreadyExists($name, $uuid)) {
            throw new \Exception($this->translator->trans('contractType.name.alreadyExists', [':name' => $name], 'contract_types'), Response::HTTP_CONFLICT);
        }
    }

    public function isContractTypeExists(string $uuid): void
    {
        $this->contractTypeReaderRepository->getContractTypeByUUID($uuid);
    }
}
