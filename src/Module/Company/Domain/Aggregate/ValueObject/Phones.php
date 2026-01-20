<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\ValueObject;

final readonly class Phones
{
    public function __construct(private array $phones)
    {
        foreach ($phones as $phone) {
            if ($phone === null || trim((string) $phone) === '') {
                continue;
            }

            if (!is_string($phone)) {
                throw new \InvalidArgumentException('Phone must be string');
            }
        }
    }

    public static function fromArray(array $phones): self
    {
        return new self($phones);
    }

    public function toArray(): array
    {
        return $this->phones;
    }
}
