<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.employee.update.validator')]
#[AutoconfigureTag('app.employee.delete.validator')]
#[AutoconfigureTag('app.employee.query.get.validator')]
final readonly class EmployeeExistsValidator implements ValidatorInterface
{
    public function __construct(private EmployeeReaderInterface $employeeReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        return true;
    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'employeeUUID')) {
            return;
        }

        $employeeUUID = $data->employeeUUID;
        $employeeExists = $this->employeeReaderRepository->isEmployeeWithUUIDExists($employeeUUID);
        if (!$employeeExists) {
            throw new \Exception($this->translator->trans('employee.uuid.notExists', [':uuid' => $employeeUUID], 'employees'), Response::HTTP_CONFLICT);
        }
    }
}
