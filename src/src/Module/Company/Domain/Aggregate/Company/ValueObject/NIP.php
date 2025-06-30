<?php

declare(strict_types=1);

namespace App\Module\Company\Domain\Aggregate\Company\ValueObject;

use App\Common\Shared\Utils\NIPValidator;
use Symfony\Component\HttpFoundation\Response;

final readonly class NIP
{
    public function __construct(private string $value)
    {
        $error = NIPValidator::validate($value);

        if ($error !== null) {
            throw new \Exception($error, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}