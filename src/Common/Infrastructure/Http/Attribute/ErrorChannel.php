<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\Http\Attribute;

use App\Common\Domain\Enum\MonologChanelEnum;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class ErrorChannel
{
    public function __construct(public MonologChanelEnum $channel = MonologChanelEnum::MAIN) {}
}