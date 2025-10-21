<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\ContractType\Writer;

use App\Module\Company\Domain\Entity\ContractType;
use App\Module\Company\Domain\Interface\ContractType\ContractTypeWriterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

final class ContractTypeWriterRepository extends ServiceEntityRepository implements ContractTypeWriterInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContractType::class);
    }

    public function saveContractTypeInDB(ContractType $contractType): void
    {
        $this->getEntityManager()->persist($contractType);
        $this->getEntityManager()->flush();
    }

    public function saveContractTypesInDB(Collection $contractTypes): void
    {
        foreach ($contractTypes as $contractType) {
            $this->getEntityManager()->persist($contractType);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteContractTypeInDB(ContractType $contractType): void
    {
        $this->getEntityManager()->remove($contractType);
        $this->getEntityManager()->flush();
    }

    public function deleteMultipleContractTypesInDB(Collection $contractTypes): void
    {
        if (empty($contractTypes)) {
            return;
        }

        foreach ($contractTypes as $contractType) {
            $this->getEntityManager()->remove($contractType);
        }

        $this->getEntityManager()->flush();
    }
}
