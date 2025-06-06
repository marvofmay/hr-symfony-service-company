<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Persistance\Repository\Doctrine\Industry\Reader;

use App\Common\Domain\Exception\NotFindByUUIDException;
use App\Module\Company\Domain\Entity\Industry;
use App\Module\Company\Domain\Interface\Industry\IndustryReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndustryReaderRepository extends ServiceEntityRepository implements IndustryReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, Industry::class);
    }

    public function getIndustryByUUID(string $uuid): ?Industry
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    public function getIndustriesByUUID(array $uuids): Collection
    {
        $industries = $this->findBy([
            Industry::COLUMN_UUID => $uuids
        ]);

        if (empty($industries)) {
            throw new NotFindByUUIDException(sprintf(
                '%s : %s',
                $this->translator->trans('industry.uuid.notFound', [], 'industries'),
                implode(', ', $uuids)
            ));
        }

        return new ArrayCollection($industries);
    }

    public function getIndustryByName(string $name, ?string $uuid = null): ?Industry
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('i')
            ->from(Industry::class, 'i')
            ->where('i.'.Industry::COLUMN_NAME.' = :name')
            ->setParameter('name', $name);

        if ($uuid !== null) {
            $qb->andWhere('i.'.Industry::COLUMN_UUID.' != :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function isIndustryExists(string $name, ?string $uuid = null): bool
    {
        return !is_null($this->getIndustryByName($name, $uuid));
    }

    public function isIndustryExistsWithUUID(string $uuid): bool
    {
        return null !== $this->findOneBy(['uuid' => $uuid]);
    }
}
