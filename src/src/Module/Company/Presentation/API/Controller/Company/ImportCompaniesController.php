<?php

declare(strict_types=1);

namespace App\Module\Company\Presentation\API\Controller\Company;

use App\Common\Domain\DTO\UploadFileDTO;
use App\Common\Domain\Enum\FileExtensionEnum;
use App\Common\Domain\Service\UploadFile\UploadFile;
use App\Common\Presentation\Action\UploadFileAction;
use App\Module\Company\Domain\DTO\Company\ImportDTO;
use App\Module\Company\Presentation\API\Action\Company\ImportCompaniesAction;
use App\Module\System\Domain\Enum\AccessEnum;
use App\Module\System\Domain\Enum\PermissionEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportCompaniesController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger, private readonly TranslatorInterface $translator,)
    {
    }

    #[Route('/api/companies/import', name: 'import', methods: ['POST'])]
    public function import(
        #[MapUploadedFile] UploadedFile $file,
        ValidatorInterface              $validator,
        UploadFileAction                $uploadFileAction,
        ImportCompaniesAction           $importCompaniesAction): JsonResponse
    {
        try {
            if (!$this->isGranted(PermissionEnum::IMPORT, AccessEnum::COMPANY)) {
                throw new \Exception($this->translator->trans('accessDenied', [], 'messages'), Response::HTTP_FORBIDDEN);
            }

            $uploadFilePath = 'src/Storage/Upload/Import/Companies';
            $fileName = UploadFile::generateUniqueFileName(FileExtensionEnum::XLSX);

            $uploadFileDTO = new UploadFileDTO($file, $uploadFilePath, $fileName);
            $errors = $validator->validate($uploadFileDTO);
            if (count($errors) > 0) {
                return new JsonResponse(
                    ['errors' => array_map(fn($e) => [
                        'field'   => $e->getPropertyPath(),
                        'message' => $e->getMessage()], iterator_to_array($errors)),
                    ],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $uploadFileAction->execute($uploadFileDTO);
            //ToDo:: save $uploadedFilePath, $fileName in "import_log" table in feature
            //ToDO:: pass uuid to ImportDTO($uuid) instead ImportDTO($uploadFilePath, $fileName)
            $importCompaniesAction->execute(new ImportDTO($uploadFilePath, $fileName));

            return new JsonResponse(['message' => $this->translator->trans('company.import.queued', [], 'companies'),], Response::HTTP_OK);
        } catch (\Exception $error) {
            $message = sprintf('%s. %s', $this->translator->trans('company.import.error', [], 'companies'), $this->translator->trans($error->getMessage()));
            $this->logger->error($message);

            return new JsonResponse(['message' => $message], $error->getCode());
        }
    }
}
