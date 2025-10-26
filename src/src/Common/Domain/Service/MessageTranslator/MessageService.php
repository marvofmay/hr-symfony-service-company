<?php

declare(strict_types=1);

namespace App\Common\Domain\Service\MessageTranslator;

use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class MessageService
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function get(string $key, array $parameters = [], string $domain = 'messages'): string
    {
        return $this->translator->trans($key, $parameters, $domain);
    }
}
