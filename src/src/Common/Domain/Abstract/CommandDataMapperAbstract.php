<?php

declare(strict_types=1);

namespace App\Common\Domain\Abstract;

use App\Common\Domain\Interface\CommandDataMapperInterface;
use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\MappableEntityInterface;

abstract class CommandDataMapperAbstract implements CommandDataMapperInterface
{
    abstract public function getType(): string;

    public function map(MappableEntityInterface $entity, CommandInterface $command): void
    {
        $entity->setName($command->name);
        $entity->setDescription($command->description);
    }
}