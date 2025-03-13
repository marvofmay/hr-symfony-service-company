<?php

namespace App\Module\Company\Structure\Validator\Constraints\Industry;

use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use App\Module\Company\Structure\Validator\Constraints\Industry\ExistingIndustryUUID;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExistingIndustryUUIDValidator extends ConstraintValidator
{
    public function __construct(private readonly IndustryReaderInterface $industryReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingIndustryUUID) {
            throw new UnexpectedTypeException($constraint, ExistingIndustryUUID::class);
        }

        if (!is_string($value) || !preg_match('/^[0-9a-fA-F-]{36}$/', $value)) {
            return;
        }

        $exists = $this->industryReaderRepository->isIndustryWithUUIDExists($value);
        if (!$exists) {
            $message = $this->translator->trans(
                $constraint->message['uuidNotExists'],
                [':uuid' => $value],
                $constraint->message['domain']
            );

            $this->context->buildViolation($message)->addViolation();
        }
    }
}
