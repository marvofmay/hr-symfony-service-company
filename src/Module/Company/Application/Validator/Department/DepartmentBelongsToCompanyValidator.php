<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Department;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.department.query.parent_department_options.validator')]
final readonly class DepartmentBelongsToCompanyValidator implements ValidatorInterface
{
    public function __construct(private DepartmentReaderInterface $departmentReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return property_exists($data, 'companyUUID') && null !== $data->companyUUID & property_exists($data, 'departmentUUID') && null !== $data->departmentUUID;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        $companyUUID = $data->companyUUID;
        $departmentUUID = $data->departmentUUID;
        $departmentExists = $this->departmentReaderRepository->isDepartmentBelongsToCompany($companyUUID, $departmentUUID);
        if (!$departmentExists) {
            throw new \Exception(
                $this->translator->trans(
                    'department.uuid.notExistsInCompany',
                    [':companyUUID' => $companyUUID, ':departmentUUID' => $departmentUUID],
                    'departments'
                ),
                Response::HTTP_CONFLICT
            );
        }
    }
}
