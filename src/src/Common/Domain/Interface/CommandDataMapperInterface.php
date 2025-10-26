<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

interface CommandDataMapperInterface
{

    public function getType(): string;
    public function map(MappableEntityInterface $entity, CommandInterface $command): void;
}