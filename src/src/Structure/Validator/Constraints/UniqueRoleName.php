<?php

namespace App\Structure\Validator\Constraints;

use App\Domain\Interface\Role\RoleReaderInterface;
use App\Domain\Repository\Role\Reader\RoleReaderRepository;
use LogicException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class UniqueRoleName extends Constraint
{
    public string $message = 'role.name.roleAlreadyExists';
}

class UniqueRoleNameValidator extends ConstraintValidator
{
    public function __construct(private readonly TranslatorInterface $translator, private readonly RoleReaderInterface $roleReaderRepository) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueRoleName) {
            throw new LogicException($this->translator->trans('role.name.expectedUniqueRoleNameConstraint'));
        }

        if ($this->isRoleNameNotUnique($value)) {
            $this->context->buildViolation($this->translator->trans($constraint->message))
                ->addViolation();
        }
    }

    private function isRoleNameNotUnique(string $value): bool
    {
        return $this->roleReaderRepository->isRoleExists($value);
    }
}
