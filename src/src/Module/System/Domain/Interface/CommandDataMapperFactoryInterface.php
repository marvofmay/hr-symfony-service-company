<?php

namespace App\Module\System\Domain\Interface;

use App\Common\Domain\Interface\CommandDataMapperInterface;
use App\Module\System\Domain\Enum\CommandDataMapperKindEnum;

interface CommandDataMapperFactoryInterface
{
    public function getMapper(CommandDataMapperKindEnum $type): CommandDataMapperInterface;
}