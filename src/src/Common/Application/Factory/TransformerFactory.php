<?php

declare(strict_types=1);

namespace App\Common\Application\Factory;

use App\Module\Company\Application\QueryHandler\Company\ListCompaniesQueryHandler;
use App\Module\Company\Application\QueryHandler\Department\ListDepartmentsQueryHandler;
use App\Module\Company\Application\QueryHandler\Role\ListRolesQueryHandler;
use App\Module\Company\Application\Transformer\Company\CompanyDataTransformer;
use App\Module\Company\Application\Transformer\Department\DepartmentDataTransformer;
use App\Module\Company\Application\Transformer\Role\RoleDataTransformer;
use App\Module\Note\Application\QueryHandler\ListNotesQueryHandler;
use App\Module\Note\Application\Transformer\NoteDataTransformer;

class TransformerFactory
{
    public static function createForHandler(string $handlerClass): object
    {
        return match ($handlerClass) {
            ListDepartmentsQueryHandler::class => new DepartmentDataTransformer(),
            ListCompaniesQueryHandler::class => new CompanyDataTransformer(),
            ListRolesQueryHandler::class => new RoleDataTransformer(),
            ListNotesQueryHandler::class => new NoteDataTransformer(),
            default => throw new \RuntimeException("No transformer found for handler: {$handlerClass}")
        };
    }
}