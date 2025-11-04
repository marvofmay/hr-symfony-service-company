<?php

namespace App\Common\Domain\Interface;

use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;

interface CommandDataMapperFactoryInterface
{
    public function getMapper(CommandDataMapperKindEnum $type): CommandDataMapperInterface;
}