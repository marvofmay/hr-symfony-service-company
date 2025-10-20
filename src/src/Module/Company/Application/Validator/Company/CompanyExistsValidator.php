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

#[AutoconfigureTag('app.company.update.validator')]
#[AutoconfigureTag('app.company.delete.validator')]
#[AutoconfigureTag('app.company.restore.validator')]
#[AutoconfigureTag('app.company.query.get.validator')]
final readonly class CompanyExistsValidator implements ValidatorInterface
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
        $uuid = $data->companyUUID;
        $companyExists = $this->companyReaderRepository->isCompanyExistsWithUUID($uuid);
        if (!$companyExists) {
            throw new \Exception($this->translator->trans('company.uuid.notExists', [':uuid' => $uuid], 'companies'), Response::HTTP_CONFLICT);
        }
    }
}
