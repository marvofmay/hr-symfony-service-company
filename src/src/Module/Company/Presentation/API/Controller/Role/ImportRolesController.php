<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Role;

use App\Common\Domain\Service\MessageTranslator\MessageService;
use App\Module\Company\Application\Facade\ImportRolesFacade;
use App\Module\System\Domain\Enum\Access\AccessEnum;
use App\Module\System\Domain\Enum\Permission\PermissionEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;

final class ImportRolesController extends AbstractController
{
    public function __construct(private readonly ImportRolesFacade $importRolesFacade, private readonly MessageService $messageService)
    {
    }

    #[Route('/api/roles/import', name: 'api.roles.import', methods: ['POST'])]
    public function import(#[MapUploadedFile] ?UploadedFile $file): JsonResponse
    {
        if (!$this->isGranted(PermissionEnum::IMPORT, AccessEnum::ROLE)) {
            return new JsonResponse(['message' => $this->messageService->get('accessDenied')], Response::HTTP_FORBIDDEN);
        }

        if (!$file) {
            return new JsonResponse(['message' => $this->messageService->get('role.import.file.required', [], 'roles')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $this->importRolesFacade->handle($file);

        $responseData = ['message' => $result['message']];
        if (!empty($result['errors'])) {
            $responseData['errors'] = $result['errors'];
        }

        return new JsonResponse($responseData, $result['success'] ? Response::HTTP_CREATED : Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
