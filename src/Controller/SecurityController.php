<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function ApiLogin(): JsonResponse
    {
        throw new LogicException('This method can be blank - it will be intercepted by the authenticator');
    }

    #[Route(path: '/api/logout', name: 'api_logout', methods: ['POST'])]
    public function ApiLogout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
