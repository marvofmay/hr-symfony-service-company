<?php

namespace App\Module\Company\Structure\Validator\Constraints\Employee;

use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExistingEmployeeUUIDValidator extends ConstraintValidator
{
    public function __construct(private readonly EmployeeReaderInterface $employeeReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingEmployeeUUID) {
            throw new UnexpectedTypeException($constraint, ExistingEmployeeUUID::class);
        }

        if (!is_string($value) || !preg_match('/^[0-9a-fA-F-]{36}$/', $value)) {
            return;
        }

        $exists = $this->employeeReaderRepository->isEmployeeWithUUIDExists($value);
        if (!$exists) {
            $message = $this->translator->trans(
                $constraint->message['uuidNotExists'],
                [':uuid' => $value],
                $constraint->message['domain']
            );

            $this->context->buildViolation($message)->addViolation();
        }
    }
}
