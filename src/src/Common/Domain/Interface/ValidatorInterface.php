<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

interface ValidatorInterface
{
    public function supports(CommandInterface|QueryInterface $data): bool;

    public function validate(CommandInterface|QueryInterface $data): void;
}
