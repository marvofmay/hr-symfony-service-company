<?php

declare(strict_types=1);

namespace App\Common\Domain\Interface;

use Ramsey\Uuid\UuidInterface;

interface UUIDValueObjectInterface
{
    public static function fromString(string $uuid): static;

    public static function generate(): static;

    public function toString(): string;

    public function equals(UUIDValueObjectInterface $other): bool;

    public function getUUID(): UuidInterface;
}
