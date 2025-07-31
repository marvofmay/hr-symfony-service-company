<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer;

use App\Module\Company\Domain\Aggregate\Employee\ValueObject\PESEL;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PESELNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof PESEL;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): string
    {
        return $data->getValue();
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_string($data) && is_a($type, PESEL::class, true);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): PESEL
    {
        return PESEL::fromString($data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            PESEL::class => true,
        ];
    }
}