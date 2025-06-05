<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\DefaultData\Data;

use App\Common\Domain\Interface\EnumInterface;

enum PositionEnum: string implements EnumInterface
{
    case WEB_APPS_DIRECTOR        = 'web_apps_director';
    case PHP_DEVELOPER            = 'php_developer';
    case JAVA_DEVELOPER           = 'java_developer';
    case PYTHON_DEVELOPER         = 'python_developer';
    case JS_DEVELOPER             = 'js_developer';
    case REACT_DEVELOPER          = 'react_developer';
    case VUE_DEVELOPER            = 'vue_developer';
    case PROJECT_MANAGER          = 'project_manager';
    case QA_TESTER                = 'qa_tester';
    case GRAPHIC_DESIGNER         = 'graphic_designer';
    case CUSTOMER_SUPPORT_AGENT   = 'customer_support_agent';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}