<?php

namespace App\Module\Company\Structure\Validator\Constraints\Role;

use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExistingRoleUUIDValidator extends ConstraintValidator
{
    public function __construct(private readonly RoleReaderInterface $roleReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingRoleUUID) {
            throw new UnexpectedTypeException($constraint, ExistingRoleUUID::class);
        }

        if (!is_string($value) || !preg_match('/^[0-9a-fA-F-]{36}$/', $value)) {
            return;
        }

        $exists = $this->roleReaderRepository->isRoleWithUUIDExists($value);
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
