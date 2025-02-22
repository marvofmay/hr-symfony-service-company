<?php

namespace App\Module\Company\Structure\Validator\Constraints\Department;

use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExistingDepartmentUUIDValidator extends ConstraintValidator
{
    public function __construct(private readonly DepartmentReaderInterface $companyReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingDepartmentUUID) {
            throw new UnexpectedTypeException($constraint, ExistingDepartmentUUID::class);
        }

        if (!is_string($value) || !preg_match('/^[0-9a-fA-F-]{36}$/', $value)) {
            return;
        }

        $exists = $this->companyReaderRepository->isDepartmentWithUUIDExists($value);
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
