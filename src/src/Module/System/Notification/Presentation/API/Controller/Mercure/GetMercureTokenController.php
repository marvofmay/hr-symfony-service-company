<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Presentation\API\Controller\Mercure;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetMercureTokenController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/api/mercure/subscriber-token', name: 'api.mercure.subscriber_token')]
    public function token(): JsonResponse
    {
        $user = $this->getUser();

        return new JsonResponse(['mercure_jwt' => '']);
    }

}
