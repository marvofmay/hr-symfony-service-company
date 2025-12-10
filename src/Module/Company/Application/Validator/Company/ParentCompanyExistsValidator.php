<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Company;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Module\Company\Domain\Interface\Company\CompanyReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.company.create.validator')]
#[AutoconfigureTag('app.company.update.validator')]
final readonly class ParentCompanyExistsValidator
{
    public function __construct(private CompanyReaderInterface $companyReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return property_exists($data, 'parentCompanyUUID') && !empty($data->parentCompanyUUID);
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'parentCompanyUUID')) {
            return;
        }

        $parentUUID = $data->parentCompanyUUID;
        $companyExists = $this->companyReaderRepository->isCompanyExistsWithUUID($parentUUID);
        if (!$companyExists) {
            throw new \Exception($this->translator->trans('company.uuid.notExists', [':uuid' => $parentUUID], 'companies'), Response::HTTP_CONFLICT);
        }
    }
}
