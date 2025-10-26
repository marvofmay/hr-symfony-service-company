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

#[AutoconfigureTag('app.company.create.validator')]
#[AutoconfigureTag('app.company.update.validator')]
final readonly class CompanyFullNameAlreadyExistsValidator implements ValidatorInterface
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
        if (!property_exists($data, 'fullName')) {
            return;
        }

        $fullName = $data->fullName;
        $companyUUID = $data->companyUUID ?? null;
        if ($this->companyReaderRepository->isCompanyExistsWithFullName($fullName, $companyUUID)) {
            throw new \Exception($this->translator->trans('company.fullName.alreadyExists', [':fullName' => $fullName], 'companies'), Response::HTTP_CONFLICT);
        }
    }
}
