<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\ContractType;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.contract_type.create.validator')]
#[AutoconfigureTag('app.contract_type.update.validator')]
final readonly class ContractTypeNameAlreadyExistsValidator implements ValidatorInterface
{
    public function __construct(
        private ContractTypeReaderInterface $contractTypeReaderRepository,
        private TranslatorInterface $translator
    )
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'name')) {
            return;
        }

        $name = $data->name;
        $contractTypeUUID = $data->contractTypeUUID ?? null;
        if ($this->contractTypeReaderRepository->isContractTypeNameAlreadyExists($name, $contractTypeUUID)) {
            throw new \Exception($this->translator->trans('contractType.name.alreadyExists', [':name' => $name], 'contract_types'), Response::HTTP_CONFLICT);
        }
    }
}
