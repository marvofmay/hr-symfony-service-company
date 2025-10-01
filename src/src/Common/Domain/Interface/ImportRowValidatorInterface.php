<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

interface ImportRowValidatorInterface
{
    public function validate(array $row, array $additionalData = []): ?string;
}