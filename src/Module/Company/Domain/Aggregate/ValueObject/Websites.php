<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\ValueObject;

final class Websites
{
    public function __construct(private array $websites)
    {
        foreach ($websites as $url) {
            if ($url === null || trim((string) $url) === '') {
                continue;
            }

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException(
                    "Invalid URL: $url"
                );
            }
        }

        $this->websites = array_values(array_filter(
            $websites,
            fn ($url) => is_string($url) && trim($url) !== ''
        ));
    }

    public static function fromArray(array $websites): self
    {
        return new self($websites);
    }

    public function toArray(): array
    {
        return $this->websites;
    }
}
