<?php

declare(strict_types=1);

namespace App\Module\Company\Infrastructure\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class AbstractUUIDNormalizer implements NormalizerInterface, DenormalizerInterface
{
    abstract protected function getSupportedClass(): string;

    abstract protected function fromString(string $value): object;

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ($this->getSupportedClass());
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): string
    {
        return $data->toString();
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === $this->getSupportedClass();
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): object
    {
        return $this->fromString((string) $data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            $this->getSupportedClass() => true,
        ];
    }
}
