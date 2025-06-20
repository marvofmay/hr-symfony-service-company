<?php

namespace App\Module\System\Structure\Validator\Constraints\Permission;

use App\Module\System\Domain\Interface\Permission\PermissionReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExistingPermissionUUIDValidator extends ConstraintValidator
{
    public function __construct(private readonly PermissionReaderInterface $permissionReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingPermissionUUID) {
            throw new UnexpectedTypeException($constraint, ExistingPermissionUUID::class);
        }

        if (!is_string($value) || !preg_match('/^[0-9a-fA-F-]{36}$/', $value)) {
            return;
        }

        $exists = $this->permissionReaderRepository->isPermissionWithUUIDExists($value);
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
