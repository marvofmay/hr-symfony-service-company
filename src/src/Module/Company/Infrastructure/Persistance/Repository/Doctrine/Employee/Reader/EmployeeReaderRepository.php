<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Employee\Reader;

use App\Common\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmployeeReaderRepository extends ServiceEntityRepository implements EmployeeReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, Employee::class);
    }

    public function getEmployeeByUUID(string $uuid): ?Employee
    {
        $position = $this->getEntityManager()
            ->createQuery('SELECT e FROM App\Module\Company\Domain\Entity\Employee e WHERE e.' . Employee::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $uuid)
            ->getOneOrNullResult();

        if (!$position) {
            throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('employee.uuid.notFound', [], 'employees'), $uuid));
        }

        return $position;
    }

    public function isEmployeeWithUUIDExists(string $uuid): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('e')
            ->from('App\Module\Company\Domain\Entity\Employee', 'e')
            ->where('e.' . Employee::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $uuid);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }
}
