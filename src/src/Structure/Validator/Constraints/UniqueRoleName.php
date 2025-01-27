<?php

namespace App\Structure\Validator\Constraints;

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
    public function __construct(private readonly TranslatorInterface $translator) {}

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
        // Tutaj można zaimplementować logikę sprawdzania unikalności, np. zapytanie do bazy danych.
        return false;
    }
}
