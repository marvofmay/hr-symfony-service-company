<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Domain\Service\Message;

use App\Common\Domain\Interface\NotifiableEventInterface;
use App\Common\Domain\Trait\ClassNameExtractorTrait;
use App\Module\System\Notification\Domain\Interface\Message\NotificationMessageCreatorInterface;
use App\Module\System\Notification\Domain\Interface\Message\NotificationResolveInterface;
use App\Module\System\Notification\Domain\Interface\Template\NotificationTemplateSettingReaderInterface;
use App\Module\System\Notification\Domain\Service\Channel\NotificationChannelDispatcher;
use App\Module\System\Notification\Domain\Service\Event\NotificationEventPayloadDispatcher;

final readonly class NotificationResolver implements NotificationResolveInterface
{
    use ClassNameExtractorTrait;

    public function __construct(
        private NotificationTemplateSettingReaderInterface $templateReaderRepository,
        private NotificationMessageCreatorInterface $messageCreator,
        private NotificationChannelDispatcher $channelDispatcher,
        private NotificationEventPayloadDispatcher $payloadDispatcher
    ) {
    }

    public function resolve(NotifiableEventInterface $notifiableEvent): void
    {
        [$payload, $recipientUUIDs] = $this->payloadDispatcher->getPayloadData($notifiableEvent);

        $notifiableEventClassName = $this->getShortClassName($notifiableEvent::class);
        $templates = $this->templateReaderRepository->getActiveByEventName($notifiableEventClassName);
        foreach ($templates as $template) {
            $event = $template->getEvent();
            $channel = $template->getChannel();
            $title = $this->renderTemplate($template->getTitle(), $payload);
            $content = $this->renderTemplate($template->getContent(), $payload);

            $this->messageCreator->create($event, $channel, $template, $title, $content, $recipientUUIDs);

            $this->channelDispatcher->dispatch($event, $channel, $recipientUUIDs, $title, $content, $payload);
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
