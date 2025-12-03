<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Module\Reader;

use App\Module\System\Domain\Entity\Module;
use App\Module\System\Domain\Interface\Module\ModuleReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

class ModuleReaderRepository extends ServiceEntityRepository implements ModuleReaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function getModules(bool $active = true): Collection
    {
        return new ArrayCollection(
            $this->createQueryBuilder(Module::ALIAS)
                ->where(Module::ALIAS.'.'. Module::COLUMN_ACTIVE .' = :active')
                ->setParameter('active', $active)
                ->getQuery()
                ->getResult()
        );
    }

    public function getModuleByUUID(string $uuid): ?Module
    {
        return $this->findOneBy([Module::COLUMN_UUID => $uuid]);
    }

    public function getModuleByName(string $name): ?Module
    {
        return $this->findOneBy([Module::COLUMN_NAME => $name]);
    }

    public function isModuleWithUUIDExists(string $uuid): bool
    {
        return null !== $this->findOneBy([Module::COLUMN_UUID => $uuid]);
    }

    public function isModuleWithNameExists(string $name): bool
    {
        return null !== $this->findOneBy([Module::COLUMN_NAME => $name]);
    }

    public function isModuleActive(string $name): bool
    {
        return null !== $this->findOneBy([Module::COLUMN_NAME => $name, Module::COLUMN_ACTIVE => true]);
    }
}
