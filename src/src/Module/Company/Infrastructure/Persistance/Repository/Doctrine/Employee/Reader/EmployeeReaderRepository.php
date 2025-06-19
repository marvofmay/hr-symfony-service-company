<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Employee\Reader;

use App\Common\Domain\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\Employee;
use App\Module\Company\Domain\Entity\User;
use App\Module\Company\Domain\Interface\Employee\EmployeeReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EmployeeReaderRepository extends ServiceEntityRepository implements EmployeeReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, Employee::class);
    }

    public function getEmployeeByUUID(string $uuid): ?Employee
    {
        return $this->getEntityManager()
            ->createQuery('SELECT e FROM ' . Employee::class . ' e WHERE e.' . Employee::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $uuid)
            ->getOneOrNullResult();
    }

    public function getEmployeesByUUID(array $selectedUUID): Collection
    {
        if (empty($selectedUUID)) {
            return new ArrayCollection();
        }

        $employees = $this->getEntityManager()
            ->createQuery('SELECT e FROM ' . Employee::class . ' e WHERE e.' . Employee::COLUMN_UUID . ' IN (:uuids)')
            ->setParameter('uuids', $selectedUUID)
            ->getResult();

        if (count($employees) !== count($selectedUUID)) {
            $missingUuids = array_diff($selectedUUID, array_map(fn($employee) => $employee->getUUID(), $employees));
            throw new NotFindByUUIDException(sprintf(
                '%s : %s',
                $this->translator->trans('employee.uuid.notFound', [], 'employees'),
                implode(', ', $missingUuids)
            ));
        }

        return new ArrayCollection($employees);
    }

    public function isEmployeeWithUUIDExists(string $uuid): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('e')
            ->from(Employee::class, 'e')
            ->where('e.' . Employee::COLUMN_UUID . ' = :uuid')
            ->setParameter('uuid', $uuid);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }

    public function getEmployeeByEmail(string $email, ?string $uuid = null): ?User
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.' . User::COLUMN_EMAIL . ' = :email')
            ->setParameter('email', $email);

        if (null !== $uuid) {
            $qb->andWhere('u.' . User::COLUMN_EMPLOYEE_UUID . ' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isEmployeeWithEmailExists(string $email, ?string $uuid = null): bool
    {
        return !is_null($this->getEmployeeByEmail($email, $uuid));
    }

    public function isEmployeeExists(string $pesel, ?string $employeeUUID = null): bool
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select(Employee::ALIAS)
            ->from(Employee::class, Employee::ALIAS)
            ->where(sprintf('%s.%s = :pesel', Employee::ALIAS, Employee::COLUMN_PESEL))
            ->setParameter('pesel', $pesel);

        if ($employeeUUID !== null) {
            $qb->andWhere(sprintf('%s.uuid != :uuid', Employee::ALIAS))
                ->setParameter('uuid', $employeeUUID);
        }

        return null !== $qb->getQuery()->getOneOrNullResult();
    }
}
