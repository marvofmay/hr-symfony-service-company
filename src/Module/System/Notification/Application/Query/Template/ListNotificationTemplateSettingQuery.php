<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Application\Query\Template;

use App\Common\Application\Query\ListQueryAbstract;
use App\Common\Domain\Interface\QueryDTOInterface;
use App\Module\System\Notification\Domain\Entity\NotificationTemplateSetting;

final class ListNotificationTemplateSettingQuery extends ListQueryAbstract
{
    public function __construct(protected QueryDTOInterface $queryDTO)
    {
        parent::__construct($queryDTO);
    }

    public function getAttributes(): array
    {
        return NotificationTemplateSetting::getAttributes();
    }

    public function getRelations(): array
    {
        return NotificationTemplateSetting::getRelations();
    }
}
