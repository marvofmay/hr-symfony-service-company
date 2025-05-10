<?php

declare(strict_types=1);

namespace App\Module\System\Infrastructure\Persistance\Repository\Doctrine\Module\Reader;

use App\Module\System\Domain\Entity\Module;
use App\Module\System\Domain\Interface\Module\ModuleReaderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ModuleReaderRepository extends ServiceEntityRepository implements ModuleReaderInterface
{
    public function __construct(ManagerRegistry $registry,)
    {
        parent::__construct($registry, Module::class);
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