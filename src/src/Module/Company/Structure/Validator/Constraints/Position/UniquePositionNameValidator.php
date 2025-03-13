<?php

declare(strict_types=1);

namespace App\Module\Company\Structure\Validator\Constraints\Position;

use App\Module\Company\Domain\Interface\Position\PositionReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniquePositionNameValidator extends ConstraintValidator
{
    public function __construct(private readonly PositionReaderInterface $positionReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof UniquePositionName) {
            throw new \InvalidArgumentException(sprintf('%s can only be used with UniquePositionName constraint.', __CLASS__));
        }

        if (!is_string($value) || empty($value)) {
            return;
        }

        $object = $this->context->getObject();
        $uuid = property_exists($object, 'uuid') ? $object->uuid : null;

        if ($this->positionReaderRepository->isPositionExists($value, $uuid)) {
            $this->context->buildViolation($this->translator->trans($constraint->message, [], 'positions'))
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
