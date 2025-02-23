<?php

declare(strict_types=1);

namespace App\Module\Company\Structure\Validator\Constraints\Employee;

use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniqueEmployeeEmailValidator extends ConstraintValidator
{
    public function __construct(private readonly EmployeeReaderInterface $employeeReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueEmployeeEmail) {
            throw new \InvalidArgumentException(sprintf('%s can only be used with EmployeeEmail constraint.', __CLASS__));
        }

        if (!is_string($value) || empty($value)) {
            return;
        }

        $object = $this->context->getObject();
        $uuid = property_exists($object, 'uuid') ? $object->uuid : null;

        if ($this->employeeReaderRepository->isEmployeeWithEmailExists($value, $uuid)) {
            $this->context->buildViolation($this->translator->trans($constraint->message, [], 'employees'))
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
