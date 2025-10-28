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

#[AutoconfigureTag('app.contractType.delete_multiple.validator')]
final readonly class ContractTypesExistsValidator implements ValidatorInterface
{
    public function __construct(private ContractTypeReaderInterface $contractTypeReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        $uuids = $data->contractTypesUUIDs ?? [];

        if (empty($uuids)) {
            return;
        }

        $foundContractTypes = $this->contractTypeReaderRepository
            ->getContractTypesByUUID($uuids)
            ->map(fn ($contractType) => $contractType->getUUID())
            ->toArray();

        $missing = array_diff($uuids, $foundContractTypes);

        if (!empty($missing)) {
            $translatedErrors = array_map(
                fn (string $uuid) => $this->translator->trans('contractType.uuid.notExists', [':uuid' => $uuid], 'contract_types'),
                $missing
            );

            throw new \Exception(implode(', ', $translatedErrors), Response::HTTP_NOT_FOUND);
        }
    }
}
