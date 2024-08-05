<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Requirement\Requirement;

class CompanyController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/companies/{id}', name: 'api_companies_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id): Response
    {
        // get the user information
        $user = $this->getUser();

        // get the company by id
        $company = $this->entityManager->getRepository(Company::class)->find($id);

        // check if the company exists
        if(!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        // check if the user is the owner of the company
        if($company->getUserId() !== $user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // return the company information
        return $this->json($company, 200, [], ['groups' => 'company:read']);
    }

    #[Route('/api/companies', name: 'api_companies_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        // get the user information
        $user = $this->getUser();

        // create a new company
        $company = new Company();
        $company->setUserId($user);

        // get the request data
        $data = json_decode($request->getContent(), true);

        // set the company data
        $company->setName($data['name']);
        $company->setAddress($data['adress']);
        $company->setSiren($data['siren']);
        $company->setSiret($data['siret']);
        $company->setTvaNumber($data['tva_number']);

        // save the company
        $this->entityManager->persist($company);
        $this->entityManager->flush();

        // return the company information
        return $this->json($company, 201, [], ['groups' => 'company:read']);
    }

    #[Route('/api/companies/{id}', name: 'api_companies_update', methods: ['PUT'], requirements: ['id' => Requirement::DIGITS])]
    public function update(int $id, Request $request): Response
    {
        // get the user information
        $user = $this->getUser();

        // get the company by id
        $company = $this->entityManager->getRepository(Company::class)->find($id);

        // check if the company exists
        if(!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        // check if the user is the owner of the company
        if($company->getUserId() !== $user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // get the request data
        $data = json_decode($request->getContent(), true);

        // set the company data
        $company->setName($data['name']);
        $company->setAdress($data['adress']);
        $company->setSiren($data['siren']);
        $company->setSiret($data['siret']);
        $company->setTvaNumber($data['tva_number']);

        // set the updated_at
        $company->setUpdatedAt(new \DateTime());

        // save the company
        $this->entityManager->flush();

        // return the company information
        return $this->json($company, 200, [], ['groups' => 'company:read']);
    }

    #[Route('/api/companies/{id}', name: 'api_companies_delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(int $id): Response
    {
        // get the user information
        $user = $this->getUser();

        // get the company by id
        $company = $this->entityManager->getRepository(Company::class)->find($id);

        // check if the company exists
        if(!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        // check if the user is the owner of the company
        if($company->getUserId() !== $user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // delete the company
        $this->entityManager->remove($company);
        $this->entityManager->flush();

        // return the success message
        return $this->json(['message' => 'Company deleted']);
    }
}
