<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Company\ValueObject;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final readonly class CompanyUUID
{
    private UuidInterface $uuid;

    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $uuid): self
    {
        return new self(Uuid::fromString($uuid));
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function equals(self $other): bool
    {
        return $this->uuid->equals($other->uuid);
    }

    public function getUUID(): UuidInterface
    {
        return $this->uuid;
    }
}
