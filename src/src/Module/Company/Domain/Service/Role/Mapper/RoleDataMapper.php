<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Service\Role\Mapper;

use App\Common\Domain\Abstract\CommandDataMapperAbstract;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.command.mapper')]
final class RoleDataMapper extends CommandDataMapperAbstract
{
    public function getType(): string
    {
        return CommandDataMapperKindEnum::COMMAND_MAPPER_ROLE->value;
    }
}
