<?php

declare(strict_types=1);

namespace App\Common\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotBlankValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotBlank) {
            throw new \InvalidArgumentException(sprintf('%s can only be used with %s', __CLASS__, NotBlank::class));
        }

        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        }

        if (is_array($value)) {
            if (empty($value)) {
                $this->context->buildViolation($constraint->message['text'])
                    ->setTranslationDomain($constraint->message['domain'])
                    ->addViolation();
            }
            return;
        }

        if (null === $value || (is_string($value) && trim($value) === '')) {
            $this->context->buildViolation($constraint->message['text'])
                ->setTranslationDomain($constraint->message['domain'])
                ->addViolation();
        }
    }
}
