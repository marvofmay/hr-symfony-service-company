<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

interface ValidatorInterface
{
    public function supports(CommandInterface $command): bool;

    public function validate(CommandInterface $command, ?string $uuid = null): void;
}