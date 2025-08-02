<?php

declare(strict_types=1);

namespace App\Module\Company\Application\Validator\Employee;

use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class EmployeeValidator
{
    public function __construct(private EmployeeReaderInterface $employeeReaderRepository, private TranslatorInterface $translator)
    {
    }

    public function isEmployeeAlreadyExists(string $email, string $pesel, ?string $uuid = null): void
    {
        if ($this->employeeReaderRepository->isEmployeeAlreadyExists($email, $pesel, $uuid)) {
            throw new \Exception(
                $this->translator->trans(
                    'employee.alreadyExists',
                    [':email' => $email, ':pesel' => $pesel], 'employees'
                ), Response::HTTP_CONFLICT
            );
        }
    }

    public function isEmployeeExists(string $uuid): void
    {
        $this->employeeReaderRepository->getEmployeeByUUID($uuid);
    }

    public function isEmployeesExists(array $uuids): void
    {
        $errors = [];
        foreach ($uuids as $uuid) {
            if (!$this->employeeReaderRepository->isEmployeeWithUUIDExists($uuid)) {
                $errors[] = $this->translator->trans('employee.uuid.notExists', [':uuid' => $uuid], 'employees');
            }
        }

        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors), Response::HTTP_NOT_FOUND);
        }
    }
}
