<?php

declare(strict_types=1);

namespace App\Common\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class MinMaxLengthValidator extends ConstraintValidator
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MinMaxLength || !is_string($value)) {
            return;
        }

        $length = mb_strlen($value);
        $domain = $constraint->message['domain'] ?? 'validators';

        if ($constraint->min === $constraint->max) {
            if ($length !== $constraint->min) {
                $translatedMessage = $this->translator->trans(
                    $constraint->message['exactMessage'] ?? 'validation.exactLength',
                    [
                        ':qty' => $constraint->min,
                    ],
                    $domain
                );

                $this->context->buildViolation($translatedMessage)->addViolation();
            }
            return;
        }

        if ($length < $constraint->min) {
            $translatedMessage = $this->translator->trans(
                $constraint->message['tooShort'] ?? 'validation.minLength',
                [
                    ':qty' => $constraint->min,
                ],
                $domain
            );

            $this->context->buildViolation($translatedMessage)->addViolation();
        }

        if ($length > $constraint->max) {
            $translatedMessage = $this->translator->trans(
                $constraint->message['tooLong'] ?? 'validation.maxLength',
                [
                    ':qty' => $constraint->max,
                ],
                $domain
            );

            $this->context->buildViolation($translatedMessage)->addViolation();
        }
    }
}
