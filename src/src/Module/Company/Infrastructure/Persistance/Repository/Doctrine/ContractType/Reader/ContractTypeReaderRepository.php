<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\ContractType\Reader;

use App\Common\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContractTypeReaderRepository extends ServiceEntityRepository implements ContractTypeReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, ContractType::class);
    }

    public function getContractTypeByUUID(string $uuid): ?ContractType
    {
        $contractTypes = $this->getEntityManager()
            ->createQuery('SELECT ct FROM App\Module\Company\Domain\Entity\ContractType ct WHERE ct.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getOneOrNullResult();

        if (!$contractTypes) {
            throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('contractType.uuid.notFound', [], 'contract_types'), $uuid));
        }

        return $contractTypes;
    }

    public function getContractTypeByName(string $name, ?string $uuid = null): ?ContractType
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('ct')
            ->from('App\Module\Company\Domain\Entity\ContractType', 'ct')
            ->where('ct.name = :name')
            ->setParameter('name', $name);

        if (null !== $uuid) {
            $qb->andWhere('p.uuid != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isContractTypeExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getContractTypeByName($name, $uuid));
    }

    public function isContractTypeWithUUIDExists(string $uuid): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('ct')
            ->from('App\Module\Company\Domain\Entity\ContractType', 'ct')
            ->where('ct.uuid = :uuid')
            ->setParameter('uuid', $uuid);

        return null === $qb->getQuery()->getOneOrNullResult();
    }
}
