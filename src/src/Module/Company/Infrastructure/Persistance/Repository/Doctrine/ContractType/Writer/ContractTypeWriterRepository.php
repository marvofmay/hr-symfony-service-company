<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\ContractType\Writer;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContractTypeWriterRepository extends ServiceEntityRepository implements ContractTypeWriterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, ContractType::class);
    }

    public function saveContractTypeInDB(ContractType $contractType): void
    {
        $this->getEntityManager()->persist($contractType);
        $this->getEntityManager()->flush();
    }

    public function updateContractTypeInDB(ContractType $contractType): void
    {
        $this->getEntityManager()->flush();
    }

    public function saveContractTypesInDB(array $contractTypes): void
    {
        foreach ($contractTypes as $contractType) {
            $this->getEntityManager()->persist($contractType);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteMultipleContractTypesInDB(array $selectedUUID): void
    {
        if (empty($selectedUUID)) {
            return;
        }

        $query = $this->getEntityManager()->createQuery('UPDATE App\Module\Company\Domain\Entity\ContractType ct SET ct.deletedAt = :deletedAt WHERE ct.uuid IN (:uuids)');
        $query->setParameter('deletedAt', (new \DateTime())->format('Y-m-d H:i:s'));
        $query->setParameter('uuids', $selectedUUID);

        $query->execute();
    }
}
