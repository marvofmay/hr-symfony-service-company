<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Enum;

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

    public function description(): string
    {
        return match ($this) {
            self::WEB_APPS_DIRECTOR      => 'Director of Web Applications Department',
            self::PHP_DEVELOPER          => 'PHP Developer',
            self::JAVA_DEVELOPER         => 'Java Developer',
            self::PYTHON_DEVELOPER       => 'Python Developer',
            self::JS_DEVELOPER           => 'JavaScript Developer',
            self::REACT_DEVELOPER        => 'React Developer',
            self::VUE_DEVELOPER          => 'Vue Developer',
            self::PROJECT_MANAGER        => 'Project Manager',
            self::QA_TESTER              => 'Quality Assurance Tester',
            self::GRAPHIC_DESIGNER       => 'Graphic Designer',
            self::CUSTOMER_SUPPORT_AGENT => 'Customer Support Specialist',
        };
    }
}