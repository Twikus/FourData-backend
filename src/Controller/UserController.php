<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/users/me', name: 'api_users_me', methods: ['GET'])]
    public function me(): Response
    {
        // get the user information
        $user = $this->getUser();

        return $this->json($user, 200, [], ['groups' => 'user:show']);
    }

    #[Route('/api/users/{id}/companies', name: 'api_users_companies', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function companies(int $id): Response
    {
        // get user by id
        $user = $this->entityManager->getRepository(User::class)->find($id);

        // check if the user exists
        if(!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        // get the companies of the user
        $companies = $user->getCompanies();

        // return the companies information
        return $this->json($companies, 200, [], ['groups' => 'company:read']);
    }
}
