<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Service\Message;

use App\Module\System\Notification\Domain\Interface\Message\NotificationMessageCreatorInterface;
use App\Module\System\Notification\Domain\Interface\Message\NotificationResolveInterface;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingReaderInterface;

final readonly class NotificationResolver implements NotificationResolveInterface
{
    public function __construct(
        private NotificationTemplateSettingReaderInterface $templateReaderRepository,
        private NotificationMessageCreatorInterface $messageCreator,
    ) {
    }

    public function resolve(string $eventName, array $recipientUUIDs, array $payload = []): void
    {
        $templates = $this->templateReaderRepository->getActiveByEventName($eventName);
        foreach ($templates as $template) {
            $event = $template->getEvent();
            $channel = $template->getChannel();
            $title = $this->renderTemplate($template->getTitle(), $payload);
            $content = $this->renderTemplate($template->getContent(), $payload);

            $this->messageCreator->create(
                $event,
                $channel,
                $template,
                $title,
                $content,
                $recipientUUIDs
            );
        }
    }

    public function renderTemplate(string $template, array $payload): string
    {
        foreach ($payload as $key => $value) {
            $template = str_replace('{{' . $key . '}}', (string)$value, $template);
        }

        return $template;
    }
}