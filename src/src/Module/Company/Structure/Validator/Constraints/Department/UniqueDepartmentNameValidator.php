<?php

declare(strict_types=1);

namespace App\Module\Company\Structure\Validator\Constraints\Department;

use App\Module\Company\Domain\Interface\Department\DepartmentReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniqueDepartmentNameValidator extends ConstraintValidator
{
    public function __construct(private readonly DepartmentReaderInterface $departmentReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueDepartmentName) {
            throw new \InvalidArgumentException(sprintf('%s can only be used with UniqueDepartmentName constraint.', __CLASS__));
        }

        if (!is_string($value) || empty($value)) {
            return;
        }

        $object = $this->context->getObject();
        $uuid = property_exists($object, 'uuid') ? $object->uuid : null;

        if ($this->departmentReaderRepository->isDepartmentExistsWithName($value, $uuid)) {
            $this->context->buildViolation($this->translator->trans($constraint->message, [], 'departments'))
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
