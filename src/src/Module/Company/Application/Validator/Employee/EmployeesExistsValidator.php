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

#[AutoconfigureTag('app.employee.delete_multiple.validator')]
final readonly class EmployeesExistsValidator implements ValidatorInterface
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
        $uuids = $data->employeesUUIDS ?? [];

        if (empty($uuids)) {
            return;
        }

        $foundEmployees = $this->employeeReaderRepository
            ->getEmployeesByUUID($uuids)
            ->map(fn ($employee) => $employee->getUUID())
            ->toArray();

        $missing = array_diff($uuids, $foundEmployees);

        if (!empty($missing)) {
            $translatedErrors = array_map(
                fn (string $uuid) => $this->translator->trans('employee.uuid.notExists', [':uuid' => $uuid], 'employees'),
                $missing
            );

            throw new \Exception(implode(', ', $translatedErrors), Response::HTTP_NOT_FOUND);
        }
    }
}
