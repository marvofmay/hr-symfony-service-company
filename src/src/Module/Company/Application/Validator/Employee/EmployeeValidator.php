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

    public function isEmployeeAlreadyExists(string $email, ?string $uuid = null): void
    {
        if ($this->employeeReaderRepository->isEmployeeWithEmailExists($email, $uuid)) {
            throw new \Exception($this->translator->trans('employee.alreadyExists', [':email' => $email], 'employees'), Response::HTTP_CONFLICT);
        }
    }

    public function isEmployeeExists(string $uuid): void
    {
        $this->employeeReaderRepository->getEmployeeByUUID($uuid);
    }
}
