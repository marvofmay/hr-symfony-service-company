<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.employee.query.parent_employee_options.validator')]
final readonly class EmployeeBelongsToDepartmentValidator implements ValidatorInterface
{
    public function __construct(private EmployeeReaderInterface $employeeReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return property_exists($data, 'departmentUUID') && null !== $data->departmentUUID & property_exists($data, 'employeeUUID') && null !== $data->employeeUUID;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        $employeeUUID = $data->employeeUUID;
        $departmentUUID = $data->departmentUUID;
        $departmentExists = $this->employeeReaderRepository->isEmployeeBelongsToDepartmentCompany(
            departmentUUID: $departmentUUID,
            employeeUUID: $employeeUUID
        );
        if (!$departmentExists) {
            throw new \Exception(
                $this->translator->trans(
                    'employee.uuid.notExistsInDepartment',
                    [':employeeUUID' => $employeeUUID, ':departmentUUID' => $departmentUUID],
                    'employees'
                ),
                Response::HTTP_CONFLICT
            );
        }
    }
}
