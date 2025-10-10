<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer;

use App\Module\Company\Domain\Aggregate\Company\ValueObject\REGON;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class REGONNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof REGON;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): string
    {
        return $data->getValue();
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_string($data) && is_a($type, REGON::class, true);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): REGON
    {
        return REGON::fromString($data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            REGON::class => true,
        ];
    }
}
