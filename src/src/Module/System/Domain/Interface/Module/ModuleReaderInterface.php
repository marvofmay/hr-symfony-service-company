<?php

declare(strict_types=1);

namespace App\Module\System\Domain\Interface\Module;

use App\Module\System\Domain\Entity\Module;

interface ModuleReaderInterface
{
    public function getModuleByUUID(string $uuid): ?Module;
    public function getModuleByName(string $name): ?Module;
    public function isModuleWithUUIDExists(string $uuid): bool;
    public function isModuleWithNameExists(string $name): bool;
    public function isModuleActive(string $name): bool;
}