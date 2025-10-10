<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\ValueObject;

use Symfony\Component\HttpFoundation\Response;

final readonly class Websites
{
    public function __construct(private array $websites)
    {
        foreach ($websites as $url) {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \Exception("Invalid URL: $url", Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
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
