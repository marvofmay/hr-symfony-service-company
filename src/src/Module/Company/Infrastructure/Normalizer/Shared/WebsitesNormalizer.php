<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer\Shared;

use App\Module\Company\Domain\Aggregate\Company\ValueObject\Websites;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class WebsitesNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Websites;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        return $data->toArray();
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Websites::class;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Websites
    {
        return Websites::fromArray($data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Websites::class => true,
        ];
    }
}