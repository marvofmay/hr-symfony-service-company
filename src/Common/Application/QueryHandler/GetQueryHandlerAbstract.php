<?php

declare(strict_types=1);

namespace App\Common\Application\QueryHandler;

use App\Common\Domain\Interface\QueryInterface;

abstract class GetQueryHandlerAbstract
{
    protected iterable $validators = [];

    protected function validate(QueryInterface $query): void
    {
        foreach ($this->validators as $validator) {
            if (method_exists($validator, 'supports') && $validator->supports($query)) {
                $validator->validate($query);
            }
        }
    }
}