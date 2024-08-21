<?php

namespace App\Controller;

use App\Entity\Company;
use App\Helper\CompanyHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompanyController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CompanyHelper $companyHelper;
    
    public function __construct(EntityManagerInterface $entityManager, CompanyHelper $companyHelper)
    {
        $this->entityManager = $entityManager;
        $this->companyHelper = $companyHelper;
    }

    #[Route('/api/companies', name: 'api_companies_index', methods: ['GET'])]
    public function index(): Response
    {
        // get the user information
        $user = $this->getUser();

        // get the companies by user
        $companies = $this->companyHelper->findCompaniesByUser($user);

        return $this->json($companies, 200, [], ['groups' => 'company:read']);
    }

    #[Route('/api/companies/{id}', name: 'api_companies_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id): Response
    {
        // get the company by id
        $company = $this->companyHelper->findCompany($id);
        if (!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        // check if the user is authorized to view the company
        if (!$this->companyHelper->isUserAuthorized($company)) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        return $this->json($company, 200, [], ['groups' => 'company:read']);
    }

    #[Route('/api/companies', name: 'api_companies_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        // get the user information
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        // get the company data
        $companyData = $this->companyHelper->getCompanyData($data, $user);
        if (isset($companyData['error'])) {
            return $this->json($companyData, 400);
        }

        // create the company
        $company = new Company();
        $company->setUserId($user);
        $this->companyHelper->setCompanyData($company, $companyData);

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        return $this->json($company, 201, [], ['groups' => 'company:read']);
    }

    #[Route('/api/companies/{id}', name: 'api_companies_update', methods: ['PUT'], requirements: ['id' => Requirement::DIGITS])]
    public function update(int $id, Request $request): Response
    {
        // get the company by id
        $company = $this->companyHelper->findCompany($id);
        if (!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        // check if the user is authorized to update the company
        if (!$this->companyHelper->isUserAuthorized($company)) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // update the company
        $data = json_decode($request->getContent(), true);
        $autoFill = $data['autoFill'] ?? false;

        // if autoFill is true, get the company data from the API
        if ($autoFill) {
            $data = $this->companyHelper->getCompanyData($data, $company->getUserId());
        }

        // set the company data
        $this->companyHelper->setCompanyData($company, $data);
        $company->setUpdatedAt(new \DateTime());

        $this->entityManager->flush();

        return $this->json($company, 200, [], ['groups' => 'company:read']);
    }

    #[Route('/api/companies/{id}', name: 'api_companies_delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(int $id): Response
    {
        // get the company by id
        $company = $this->companyHelper->findCompany($id);
        if (!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        // check if the user is authorized to delete the company
        if (!$this->companyHelper->isUserAuthorized($company)) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // delete the company
        $this->entityManager->remove($company);
        $this->entityManager->flush();

        return $this->json(['message' => 'Company deleted']);
    }
}
