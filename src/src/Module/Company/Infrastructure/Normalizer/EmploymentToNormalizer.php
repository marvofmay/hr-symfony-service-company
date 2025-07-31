<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer;

use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmploymentFrom;
use App\Module\Company\Domain\Aggregate\Employee\ValueObject\EmploymentTo;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EmploymentToNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof EmploymentTo;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): ?string
    {
        return $data->getValue();
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === EmploymentTo::class;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): EmploymentTo
    {
        $from = $context['employmentFrom'] ?? throw new \InvalidArgumentException('Missing "employmentFrom" in context');

        return EmploymentTo::fromString($data, EmploymentFrom::fromString($from));
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            EmploymentTo::class => true,
        ];
    }
}