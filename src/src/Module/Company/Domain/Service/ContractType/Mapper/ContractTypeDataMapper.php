<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\ContractType\Mapper;

use App\Common\Domain\Abstract\CommandDataMapperAbstract;
use App\Common\Domain\Interface\CommandInterface;
use App\Common\Domain\Interface\MappableEntityInterface;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.command.mapper')]
final class ContractTypeDataMapper extends CommandDataMapperAbstract
{
    public function getType(): string
    {
        return CommandDataMapperKindEnum::COMMAND_MAPPER_CONTRACT_TYPE->value;
    }

    public function map(MappableEntityInterface $entity, CommandInterface $command): void
    {
        parent::map($entity, $command);

        if (!property_exists($command, 'active') || !method_exists($entity, 'setActive')) {
            return;
        }

        $entity->setActive($command->active);
    }
}
