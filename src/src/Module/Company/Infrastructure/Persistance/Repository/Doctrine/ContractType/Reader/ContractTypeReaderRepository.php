<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\ContractType\Reader;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContractTypeReaderRepository extends ServiceEntityRepository implements ContractTypeReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, ContractType::class);
    }

    public function getContractTypeByUUID(string $uuid): ?ContractType
    {
        return $this->findOneBy([ContractType::COLUMN_UUID => $uuid]);
    }

    public function getContractTypesByUUID(array $selectedUUID): Collection
    {
        if (!$selectedUUID) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(ContractType::ALIAS)
            ->from(ContractType::class, ContractType::ALIAS)
            ->where(ContractType::ALIAS.'.'.ContractType::COLUMN_UUID.' IN (:uuids)')
            ->setParameter('uuids', $selectedUUID);

        $contractTypes = $qb->getQuery()->getResult();

        // $foundUUIDs = array_map(fn(ContractType $contractType) => $contractType->getUUID(), $contractTypes);
        // $missingUUIDs = array_diff($selectedUUID, $foundUUIDs);
        //
        // if ($missingUUIDs) {
        //    throw new NotFindByUUIDException(
        //        sprintf(
        //            '%s : %s',
        //            $this->translator->trans('contractType.uuid.notFound', [], 'contract_types'),
        //            implode(', ', $missingUUIDs)
        //        ));
        // }

        return new ArrayCollection($contractTypes);
    }

    public function getContractTypeByName(string $name, ?string $uuid = null): ?ContractType
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('ct')
            ->from(ContractType::class, 'ct')
            ->where('ct.'.ContractType::COLUMN_NAME.' = :name')
            ->setParameter('name', $name);

        if ($uuid) {
            $qb->andWhere('ct.'.ContractType::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isContractTypeNameAlreadyExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getContractTypeByName($name, $uuid));
    }

    public function isContractTypeWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy(['uuid' => $uuid]);
    }
}
