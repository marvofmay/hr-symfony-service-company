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
#[AutoconfigureTag('app.company.query.get.validator')]
#[AutoconfigureTag('app.department.create.validator')]
#[AutoconfigureTag('app.department.update.validator')]
#[AutoconfigureTag('app.company.query.parent_company_options.validator')]
#[AutoconfigureTag('app.department.query.parent_department_options.validator')]
#[AutoconfigureTag('app.employee.query.parent_employee_options.validator')]
final readonly class CompanyExistsValidator implements ValidatorInterface
{
    public function __construct(private CompanyReaderInterface $companyReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return property_exists($data, 'companyUUID') && null !== $data->companyUUID;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        $uuid = $data->companyUUID;
        $companyExists = $this->companyReaderRepository->isCompanyExistsWithUUID($uuid);
        if (!$companyExists) {
            throw new \Exception($this->translator->trans('company.uuid.notExists', [':uuid' => $uuid], 'companies'), Response::HTTP_NOT_FOUND);
        }
    }
}
