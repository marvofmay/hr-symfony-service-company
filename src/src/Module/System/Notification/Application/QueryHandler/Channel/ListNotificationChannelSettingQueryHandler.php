<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\QueryHandler\Channel;

use App\Common\Application\Factory\TransformerFactory;
use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\System\Notification\Application\Event\Channel\NotificationChannelSettingsListedEvent;
use App\Module\System\Notification\Application\Query\Channel\ListNotificationChannelSettingQuery;
use App\Module\System\Notification\Domain\Entity\NotificationChannelSetting;
use App\Module\System\Notification\Domain\Enum\NotificationChannelSettingEntityFieldEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListNotificationChannelSettingQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        protected TransformerFactory $transformerFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
        parent::__construct($entityManager, $transformerFactory);
    }

    public function __invoke(ListNotificationChannelSettingQuery $query): array
    {
        $this->eventDispatcher->dispatch(new NotificationChannelSettingsListedEvent([$query]));

        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return NotificationChannelSetting::class;
    }

    public function getAlias(): string
    {
        return NotificationChannelSetting::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return TimeStampableEntityFieldEnum::CREATED_AT->value;
    }

    public function getAllowedFilters(): array
    {
        return [
            NotificationChannelSettingEntityFieldEnum::CHANNEL_CODE->value,
            NotificationChannelSettingEntityFieldEnum::ENABLED->value,
            TimeStampableEntityFieldEnum::CREATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            NotificationChannelSettingEntityFieldEnum::CHANNEL_CODE->value,
            NotificationChannelSettingEntityFieldEnum::ENABLED->value,
        ];
    }

    public function getRelations(): array
    {
        return NotificationChannelSetting::getRelations();
    }
}
