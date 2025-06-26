<?php

declare(strict_types=1);

namespace App\Module\Company\Structure\Validator\Constraints\ContractType;

use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniqueContractTypeNameValidator extends ConstraintValidator
{
    public function __construct(private readonly ContractTypeReaderInterface $contractTypeReaderRepository, private readonly TranslatorInterface $translator)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueContractTypeName) {
            throw new \InvalidArgumentException(sprintf('%s can only be used with UniquePositionName constraint.', __CLASS__));
        }

        if (!is_string($value) || empty($value)) {
            return;
        }

        $object = $this->context->getObject();
        $uuid = property_exists($object, 'uuid') ? $object->uuid : null;

        if ($this->contractTypeReaderRepository->isContractTypeExists($value, $uuid)) {
            $this->context->buildViolation($this->translator->trans($constraint->message, [], 'contract_types'))
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
