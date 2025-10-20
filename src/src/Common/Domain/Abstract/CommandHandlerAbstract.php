<?php

declare(strict_types=1);

namespace App\Common\Domain\Abstract;

use App\Common\Domain\Interface\CommandInterface;

class CommandHandlerAbstract
{
    protected iterable $validators = [];

    protected function validate(CommandInterface $command): void
    {
        foreach ($this->validators as $validator) {
            if (method_exists($validator, 'supports') && $validator->supports($command)) {
                $validator->validate($command);
            }
        }
    }
}
