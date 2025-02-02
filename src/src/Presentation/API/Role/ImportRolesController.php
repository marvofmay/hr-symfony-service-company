<?php

declare(strict_types = 1);

namespace App\Presentation\API\Role;

use App\Domain\Action\Role\ImportRolesAction;
use App\Domain\DTO\Role\ImportDTO;
use App\Domain\Service\UploadFile;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Service\Role\ImportRolesFromXLSX;

use Exception;

#[Route('/api/roles/import', name: 'api.roles.')]
class ImportRolesController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator) {}

    #[Route('', name: 'import', methods: ['POST'])]
    public function import(Request $request, ImportRolesAction $importRolesAction): JsonResponse
    {
        try {
            $uploadFilePath = '../src/Structure/Storage/Upload/Import/Roles';
            $uploadedFile = $request->files->get('file');

            if (!$uploadedFile) {
                return new JsonResponse(
                    ['message' => $this->translator->trans('role.import.fileRequired')],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $uploadFileService = new UploadFile($uploadFilePath, 'xlsx');
            $uploadFileService->uploadFile($uploadedFile);

            $importer = new ImportRolesFromXLSX(sprintf('%s/%s', $uploadFilePath, $uploadFileService->getFileName()));
            $data = $importer->import();

//            if (!empty($errors)) {
//                echo '<pre>';
//                print_r($errors);
//                echo '</pre>';
//            }

            $importRolesAction->execute(new ImportDTO($data));

            return new JsonResponse([
                    'success' => empty($importer->getErrors()),
                    'message' => $this->translator->trans('role.import.success'),
                    'errors' => $importer->getErrors(),
                ],
                Response::HTTP_OK
            );
        } catch (Exception $error) {
            $this->logger->error(
                sprintf('%s: %s', $this->translator->trans('role.import.error'), $this->translator->trans($error->getMessage()))
            );

            return new JsonResponse(
                ['message' => sprintf('%s - %s', $this->translator->trans('role.import.error'), $this->translator->trans($error->getMessage()))],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}