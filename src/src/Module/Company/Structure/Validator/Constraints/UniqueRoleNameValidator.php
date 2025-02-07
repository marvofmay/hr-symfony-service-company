<?php

//namespace App\Structure\Validator\Constraints;
//
//use App\Domain\Interface\Role\RoleReaderInterface;
//use Symfony\Component\Validator\Constraint;
//use Symfony\Component\Validator\ConstraintValidator;
//use App\Domain\Repository\Role\Reader\RoleReaderRepository;
//use InvalidArgumentException;
//
//class UniqueRoleNameValidator extends ConstraintValidator
//{
//    private RoleReaderRepository $roleRepository;
//
//    public function __construct(RoleReaderInterface $roleRepository)
//    {
//        $this->roleRepository = $roleRepository;
//    }
//
//    public function validate($value, Constraint $constraint): void
//    {
//        if (!$constraint instanceof UniqueRoleName) {
//            throw new InvalidArgumentException('Invalid constraint type');
//        }
//
//        if ($this->roleRepository->getRoleByName($value)) {
//            $this->context->buildViolation($constraint->message)
//                ->setParameter('{{ name }}', $value)
//                ->addViolation();
//        }
//    }
//}

namespace App\Module\Company\Structure\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Module\Company\Domain\Repository\Role\Reader\RoleReaderRepository;

class UniqueRoleNameValidator extends ConstraintValidator
{
    public function __construct(private readonly RoleReaderRepository $roleRepository) {}

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