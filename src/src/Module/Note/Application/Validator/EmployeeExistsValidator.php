<?php

declare(strict_types=1);

namespace App\Module\Note\Application\Validator;

use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\QueryInterface;
use App\Common\Domain\Interface\ValidatorInterface;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AutoconfigureTag('app.note.query.list.validator')]
final readonly class EmployeeExistsValidator implements ValidatorInterface
{
    public function __construct(
        private EmployeeReaderInterface $employeeReaderRepository,
        private TranslatorInterface $translator
    )
    {
    }

    public function supports(CommandInterface|QueryInterface $data): bool
    {
        $employeeUUID = $data->getQueryDTO()->employee;

        return is_string($employeeUUID) && $employeeUUID !== 'null';

    }

    public function validate(CommandInterface|QueryInterface $data): void
    {
        if (!property_exists($data, 'queryDTO')) {
            return;
        }

        if (!property_exists($data->getQueryDTO(), 'employee')) {
            return;
        }

        $employeeUUID = $data->getQueryDTO()->employee;

        if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/', $employeeUUID)) {
            throw new \Exception($this->translator->trans('uuid.invalid', [], 'validators'), Response::HTTP_CONFLICT);
        }

        $employee = $this->employeeReaderRepository->getEmployeeByUUID($employeeUUID);
        if (null === $employee) {
            throw new \Exception($this->translator->trans('employee.uuid.notExists', [':uuid' => $employeeUUID], 'employees'), Response::HTTP_CONFLICT);
        }
    }
}
