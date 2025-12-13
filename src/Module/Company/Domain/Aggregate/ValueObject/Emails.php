<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\ValueObject;

final class Emails
{
    public function __construct(private array $emails)
    {
        foreach ($emails as $email) {
            if ($email === null || trim((string) $email) === '') {
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException(
                    "Invalid email: $email"
                );
            }
        }

        $this->emails = array_values(array_filter(
            $emails,
            fn ($email) => is_string($email) && trim($email) !== ''
        ));
    }

    public static function fromArray(array $emails): self
    {
        return new self($emails);
    }

    public function toArray(): array
    {
        return $this->emails;
    }
}
