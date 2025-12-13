<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer;

use App\Module\Company\Domain\Aggregate\Company\ValueObject\CompanyNameAbstract;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CompanyNameNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof CompanyNameAbstract;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): ?string
    {
        return $data->getValue();
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_string($data) && is_a($type, CompanyNameAbstract::class, true);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): CompanyNameAbstract
    {
        return $type::fromString($data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            CompanyNameAbstract::class => true,
        ];
    }
}
