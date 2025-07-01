<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer\Company;

use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyUUID;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CompanyUUIDNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof CompanyUUID;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): string
    {
        return $data->toString();
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === CompanyUUID::class;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): CompanyUUID
    {
        return CompanyUUID::fromString($data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            CompanyUUID::class => true,
        ];
    }
}