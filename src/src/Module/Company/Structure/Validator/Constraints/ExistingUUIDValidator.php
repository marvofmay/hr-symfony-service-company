<?php

namespace App\Module\Company\Structure\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use App\Module\Company\Domain\Entity\Role;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExistingUUIDValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingUUID) {
            throw new UnexpectedTypeException($constraint, ExistingUUID::class);
        }

        if (!is_string($value) || !preg_match('/^[0-9a-fA-F-]{36}$/', $value)) {
            return;
        }

        // @ToDo move to repository
        $exists = $this->entityManager
            ->getRepository(Role::class)
            ->createQueryBuilder('r')
            ->select('COUNT(r.uuid)')
            ->where('r.uuid = :uuid')
            ->setParameter('uuid', $value)
            ->getQuery()
            ->getSingleScalarResult();

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