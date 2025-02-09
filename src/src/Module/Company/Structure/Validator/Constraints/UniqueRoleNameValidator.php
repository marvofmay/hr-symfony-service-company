<?php

declare(strict_types = 1);

namespace App\Module\Company\Structure\Validator\Constraints;

use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueRoleNameValidator extends ConstraintValidator
{
    public function __construct(private readonly RoleReaderInterface $roleRepository) {}

    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        $dto = $this->context->getObject();
        $uuid = method_exists($dto, 'getUuid') ? $dto->getUuid() : null;
        $existingRole = $this->roleRepository->getRoleByName($value);
        if ($existingRole && ($uuid === null || $existingRole->getUuid()->toString() !== $uuid)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ name }}', $value)
                ->addViolation();
        }
    }
}