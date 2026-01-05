<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Factory;

use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class NotificationTemplateFactory
{
    public function __construct(#[AutowireIterator(tag: 'app.notification.template')] private iterable $templates)
    {
    }

    public function getTemplate(bool $isDefault): ?NotificationTemplateInterface
    {
        foreach ($this->templates as $template) {
            if ($template->isDefault() === $isDefault) {
                return $template;
            }
        }

        return null;
    }

    public function all(): array
    {
        return iterator_to_array($this->templates);
    }
}
