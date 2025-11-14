<?php

declare(strict_types=1);

namespace App\Module\System\Notification\Presentation\API\Controller\Mercure;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetMercureTokenController extends AbstractController
{

    #[Route('/api/mercure/subscriber-token', name:'api.mercure.subscriber_token')]
    public function __invoke(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $privateKeyPath = $this->getParameter('kernel.project_dir') . '/config/jwt/private.pem';
        $privateKey = InMemory::file($privateKeyPath);
        $publicKeyPath = $this->getParameter('kernel.project_dir') . '/config/jwt/public.pem';
        $publicKey = InMemory::file($publicKeyPath);

        $config = Configuration::forAsymmetricSigner(
            new Sha256(),
            $privateKey,
            $publicKey
        );

        $now = new \DateTimeImmutable();
        $token = $config->builder()
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('mercure', [
                'subscribe' => ['user.' . $user->getUuid()]
            ])
            ->getToken($config->signer(), $config->signingKey());

        return new JsonResponse(['token' => $token->toString()]);
    }
}
