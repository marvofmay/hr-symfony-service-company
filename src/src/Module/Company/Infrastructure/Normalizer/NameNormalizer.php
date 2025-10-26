<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer;

use App\Module\Company\Domain\Aggregate\Employee\ValueObject\FirstName;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\LastName;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\NameAbstract;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NameNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof NameAbstract;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): string
    {
        return $data->getValue();
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_string($data) && is_a($type, NameAbstract::class, true);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): NameAbstract
    {
        return $type::fromString($data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            NameAbstract::class => true,
            FirstName::class => true,
            LastName::class => true,
        ];
    }
}
