<?php

declare(strict_types=1);

namespace App\Common\Application\Factory;

use App\Common\Domain\Exception\TransformerNotFoundException;
use App\Common\Domain\Interface\DataTransformerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class TransformerFactory
{
    private array $transformers = [];

    public function __construct(#[AutowireIterator(tag: 'app.data_transformer')] private readonly iterable $taggedTransformers)
    {
        foreach ($this->taggedTransformers as $taggedTransformer) {
            $this->transformers[$taggedTransformer::supports()] = $taggedTransformer;
        }
    }

    public function createForHandler(string $handlerClass): DataTransformerInterface
    {
        if (!isset($this->transformers[$handlerClass])) {
            throw new TransformerNotFoundException("No transformer found for handler: {$handlerClass}");
        }

        return $this->transformers[$handlerClass];
    }
}
