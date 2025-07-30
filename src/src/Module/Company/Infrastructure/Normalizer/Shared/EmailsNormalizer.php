<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer\Shared;

use App\Module\Company\Domain\Aggregate\Company\ValueObject\Emails;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EmailsNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Emails;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        return $data->toArray();
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Emails::class;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Emails
    {
        return Emails::fromArray($data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Emails::class => true,
        ];
    }
}