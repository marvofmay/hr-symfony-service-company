<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.company.delete_multiple.validator')]
final readonly class CompaniesExistsValidator implements ValidatorInterface
{
    public function __construct(private CompanyReaderInterface $companyReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        $uuids = $data->selectedUUIDs ?? [];

        if (empty($uuids)) {
            return;
        }

        $foundCompanies = $this->companyReaderRepository
            ->getCompaniesByUUID($uuids)
            ->map(fn ($company) => $company->getUUID())
            ->toArray();

        $missing = array_diff($uuids, $foundCompanies);

        if (!empty($missing)) {
            $translatedErrors = array_map(
                fn (string $uuid) => $this->translator->trans('company.uuid.notExists', [':uuid' => $uuid], 'companies'),
                $missing
            );

            throw new \Exception(implode(', ', $translatedErrors), Response::HTTP_NOT_FOUND);
        }
    }
}
