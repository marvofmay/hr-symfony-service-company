<?php

namespace App\Module\Company\Structure\Validator\Constraints\ContractType;

use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExistingContractTypeUUIDValidator extends ConstraintValidator
{
    public function __construct(private readonly ContractTypeReaderInterface $contractTypeReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingContractTypeUUID) {
            throw new UnexpectedTypeException($constraint, ExistingContractTypeUUID::class);
        }

        if (!is_string($value) || !preg_match('/^[0-9a-fA-F-]{36}$/', $value)) {
            return;
        }

        $exists = $this->contractTypeReaderRepository->isContractTypeWithUUIDExists($value);
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
