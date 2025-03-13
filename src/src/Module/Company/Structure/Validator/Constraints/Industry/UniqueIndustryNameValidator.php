<?php

declare(strict_types=1);

namespace App\Module\Company\Structure\Validator\Constraints\Industry;

use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniqueIndustryNameValidator extends ConstraintValidator
{
    public function __construct(private readonly IndustryReaderInterface $roleReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueIndustryName) {
            throw new \InvalidArgumentException(sprintf('%s can only be used with UniqueIndustryName constraint.', __CLASS__));
        }

        if (!is_string($value) || empty($value)) {
            return;
        }

        $object = $this->context->getObject();
        $uuid = property_exists($object, 'uuid') ? $object->uuid : null;

        if ($this->roleReaderRepository->isIndustryExists($value, $uuid)) {
            $this->context->buildViolation($this->translator->trans($constraint->message, [], 'industries'))
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
