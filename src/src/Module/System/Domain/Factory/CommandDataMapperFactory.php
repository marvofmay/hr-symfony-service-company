<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Factory;

use App\Common\Domain\Interface\CommandDataMapperFactoryInterface;
use App\Common\Domain\Interface\CommandDataMapperInterface;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class CommandDataMapperFactory implements CommandDataMapperFactoryInterface
{
    private array $mappers;

    public function __construct(#[AutowireIterator(tag: 'app.command.mapper')] private readonly iterable $taggedMappers)
    {
        $this->mappers = [];
        foreach ($this->taggedMappers as $mapper) {
            $this->mappers[$mapper->getType()] = $mapper;
        }
    }

    public function getMapper(CommandDataMapperKindEnum $type): CommandDataMapperInterface
    {
        $mapper = $this->mappers[$type->value] ?? null;
        if (!$mapper) {
            throw new \InvalidArgumentException("Command mapper not found for type: {$type->value}");
        }

        return $mapper;
    }
}
