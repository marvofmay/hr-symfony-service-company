<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

interface XLSXIteratorInterface
{
    public function loadFile(): void;

    public function iterateRows(): array;

    public function validateRow(array $row): ?string;

    public function getErrors(): array;
}
