<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\QueryHandler\Message;

use App\Common\Application\Factory\TransformerFactory;
use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Common\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\Note\Application\Query\ListNotesQuery;
use App\Module\System\Notification\Application\Event\Message\NotificationMessageListedEvent;
use App\Module\System\Notification\Application\Query\Message\ListNotificationMessagesQuery;
use App\Module\System\Notification\Domain\Entity\NotificationMessage;
use App\Module\System\Notification\Domain\Enum\NotificationMessageEntityFieldEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class ListNotificationMessagesQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TransformerFactory $transformerFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
       // #[AutowireIterator(tag: 'app.notification-messages.query.list.validator')] protected iterable $validators,
    ) {
        parent::__construct($entityManager, $transformerFactory);
    }

    public function __invoke(ListNotificationMessagesQuery $query): array
    {
        $this->validate($query);

        $this->eventDispatcher->dispatch(new NotificationMessageListedEvent([$query]));

        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return NotificationMessage::class;
    }

    public function getAlias(): string
    {
        return NotificationMessage::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return TimeStampableEntityFieldEnum::CREATED_AT->value;
    }

    public function getAllowedFilters(): array
    {
        return [
            NotificationMessageEntityFieldEnum::TITLE->value,
            NotificationMessageEntityFieldEnum::CONTENT->value,
            TimeStampableEntityFieldEnum::CREATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
            TimeStampableEntityFieldEnum::DELETED_AT->value,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            NotificationMessageEntityFieldEnum::TITLE->value,
            NotificationMessageEntityFieldEnum::CONTENT->value,
        ];
    }

    public function getRelations(): array
    {
        return NotificationMessage::getRelations();
    }

}
