<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer;

use App\Module\Company\Domain\Aggregate\ValueObject\Phones;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PhonesNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Phones;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        return $data->toArray();
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return Phones::class === $type;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Phones
    {
        return Phones::fromArray($data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Phones::class => true,
        ];
    }
}
