<?php

declare(strict_types=1);

namespace App\Module\Company\Structure\Validator\Constraints\Role;

use App\Module\Company\Domain\Interface\Role\RoleReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniqueRoleNameValidator extends ConstraintValidator
{
    public function __construct(private readonly RoleReaderInterface $roleReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueRoleName) {
            throw new \InvalidArgumentException(sprintf('%s can only be used with UniqueRoleName constraint.', __CLASS__));
        }

        if (!is_string($value) || empty($value)) {
            return;
        }

        $object = $this->context->getObject();
        $uuid = property_exists($object, 'uuid') ? $object->uuid : null;

        if ($this->roleReaderRepository->isRoleExists($value, $uuid)) {
            $this->context->buildViolation($this->translator->trans($constraint->message, [], 'roles'))
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
