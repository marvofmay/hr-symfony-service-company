<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\QueryHandler\Template;

use App\Common\Application\Factory\TransformerFactory;
use App\Common\Application\QueryHandler\ListQueryHandlerAbstract;
use App\Module\Company\Domain\Enum\TimeStampableEntityFieldEnum;
use App\Module\System\Notification\Application\Event\Template\NotificationTemplateSettingListedEvent;
use App\Module\System\Notification\Application\Query\Template\ListNotificationTemplateSettingQuery;
use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;
use App\Module\System\Notification\Domain\Enum\NotificationTemplateSettingEntityFieldEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class ListNotificationTemplateSettingQueryHandler extends ListQueryHandlerAbstract
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        protected TransformerFactory $transformerFactory,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
        parent::__construct($entityManager, $transformerFactory);
    }

    public function __invoke(ListNotificationTemplateSettingQuery $query): array
    {
        $this->eventDispatcher->dispatch(new NotificationTemplateSettingListedEvent([$query]));

        return $this->handle($query);
    }

    public function getEntityClass(): string
    {
        return NotificationTemplateSetting::class;
    }

    public function getAlias(): string
    {
        return NotificationTemplateSetting::ALIAS;
    }

    public function getDefaultOrderBy(): string
    {
        return TimeStampableEntityFieldEnum::CREATED_AT->value;
    }

    public function getAllowedFilters(): array
    {
        return [
            NotificationTemplateSettingEntityFieldEnum::EVENT_NANE->value,
            NotificationTemplateSettingEntityFieldEnum::CHANNEL_CODE->value,
            NotificationTemplateSettingEntityFieldEnum::TITLE->value,
            NotificationTemplateSettingEntityFieldEnum::CONTENT->value,
            NotificationTemplateSettingEntityFieldEnum::IS_DEFAULT->value,
            TimeStampableEntityFieldEnum::CREATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
            TimeStampableEntityFieldEnum::UPDATED_AT->value,
        ];
    }

    public function getPhraseSearchColumns(): array
    {
        return [
            NotificationTemplateSettingEntityFieldEnum::EVENT_NANE->value,
            NotificationTemplateSettingEntityFieldEnum::CHANNEL_CODE->value,
            NotificationTemplateSettingEntityFieldEnum::TITLE->value,
            NotificationTemplateSettingEntityFieldEnum::CONTENT->value,
            NotificationTemplateSettingEntityFieldEnum::IS_DEFAULT->value,
        ];
    }

    public function getRelations(): array
    {
        return NotificationTemplateSetting::getRelations();
    }
}
