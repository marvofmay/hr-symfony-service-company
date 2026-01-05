<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\QueryHandler\Event;

use App\Common\Application\Factory\TransformerFactory;
use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Common\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\System\Notification\Application\Event\Event\NotificationEventSettingsListedEvent;
use App\Module\System\Notification\Application\Query\Event\ListNotificationEventSettingQuery;
use App\Module\System\Notification\Domain\Entity\NotificationEventSetting;
use App\Module\System\Notification\Domain\Enum\NotificationEventSettingEntityFieldEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListNotificationEventSettingQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        protected TransformerFactory $transformerFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($entityManager, $transformerFactory);
    }

    public function __invoke(ListNotificationEventSettingQuery $query): array
    {
        $this->eventDispatcher->dispatch(new NotificationEventSettingsListedEvent([$query]));

        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return NotificationEventSetting::class;
    }

    public function getAlias(): string
    {
        return NotificationEventSetting::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return TimeStampableEntityFieldEnum::CREATED_AT->value;
    }

    public function getAllowedFilters(): array
    {
        return [
            NotificationEventSettingEntityFieldEnum::EVENT_NAME->value,
            NotificationEventSettingEntityFieldEnum::ENABLED->value,
            TimeStampableEntityFieldEnum::CREATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            NotificationEventSettingEntityFieldEnum::EVENT_NAME->value,
            NotificationEventSettingEntityFieldEnum::ENABLED->value,
        ];
    }

    public function getRelations(): array
    {
        return NotificationEventSetting::getRelations();
    }
}
