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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class IndustryReaderRepository extends ServiceEntityRepository implements IndustryReaderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly TranslatorInterface $translator)
    {
        parent::__construct($registry, Industry::class);
    }

    public function getIndustryByUUID(string $uuid): Industry
    {
        $industry = $this->findOneBy([Industry::COLUMN_UUID => $uuid]);
        if (null === $industry) {
            throw new \Exception($this->translator->trans('industry.uuid.notExists', [':uuid' => $uuid], 'industries'), Response::HTTP_NOT_FOUND);
        }

        return $industry;
    }

    public function getIndustriesByUUID(array $selectedUUID): Collection
    {
        if (!$selectedUUID) {
            return new ArrayCollection();
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(Industry::ALIAS)
            ->from(Industry::class, Industry::ALIAS)
            ->where(Industry::ALIAS.'.'.Industry::COLUMN_UUID.' IN (:uuids)')
            ->setParameter('uuids', $selectedUUID);

        $industries = $qb->getQuery()->getResult();

        //$foundUUIDs = array_map(fn (Industry $industry) => $industry->getUUID(), $industries);
        //$missingUUIDs = array_diff($selectedUUID, $foundUUIDs);
        //
        //if ($missingUUIDs) {
        //    throw new NotFindByUUIDException(sprintf('%s : %s', $this->translator->trans('industry.uuid.notFound', [], 'industries'), implode(', ', $missingUUIDs)));
        //}

        return new ArrayCollection($industries);
    }

    public function getIndustryByName(string $name, ?string $uuid = null): ?Industry
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('i')
            ->from(Industry::class, 'i')
            ->where('i.'.Industry::COLUMN_NAME.' = :name')
            ->setParameter('name', $name);

        if (null !== $uuid) {
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
