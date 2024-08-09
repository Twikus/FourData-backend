<?php

namespace App\Helper;

use App\Transformer\CompanySirenTransformer;
use App\Transformer\CompanySiretTransformer;
use App\Entity\User;
use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CompanyHelper
{
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;
    private Security $security;
    private CompanySirenTransformer $companySirenTransformer;
    private CompanySiretTransformer $companySiretTransformer;

    public function __construct(
        HttpClientInterface $client, 
        EntityManagerInterface $entityManager, 
        Security $security, 
        CompanySirenTransformer $companySirenTransformer,
        CompanySiretTransformer $companySiretTransformer,)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->companySirenTransformer = $companySirenTransformer;
        $this->companySiretTransformer = $companySiretTransformer;
    }

    public function getCompanyBySiren(int $siren): array
    {
        $response = $this->client->request('GET', $_ENV['SIREN_API_URL'].'/unites_legales/'.$siren, [
            'headers' => [
                'X-Client-Secret' => $_ENV['SIREN_API_TOKEN']
            ]
        ]);

        $data = $response->toArray();
        
        return $this->companySirenTransformer->transform($data);
    }

    public function getCompanyBySiret(int $siret): array
    {
        $response = $this->client->request('GET', $_ENV['SIREN_API_URL'].'/etablissements/'.$siret, [
            'headers' => [
                'X-Client-Secret' => $_ENV['SIREN_API_TOKEN']
            ]
        ]);

        $data = $response->toArray();
        
        return $this->companySiretTransformer->transform($data);
    }

    public function findCompany(int $id = null): ?Company
    {
        return $this->entityManager->getRepository(Company::class)->find($id);
    }

    public function isUserAuthorized(Company $company): bool
    {
        $user = $this->security->getUser();

        return $company->getUserId() === $user;
    }

    public function getCompanyData(array $data, User $user): array
    {
        $companyRepository = $this->entityManager->getRepository(Company::class);

        if (isset($data['siren'])) {
            $existingCompany = $companyRepository->findOneBy(['siren' => $data['siren'], 'user' => $user]);
            if ($existingCompany) {
                return ['error' => 'Company already exists'];
            }
            return $this->getCompanyBySiren($data['siren']);
        }

        if (isset($data['siret'])) {
            $existingCompany = $companyRepository->findOneBy(['siret' => $data['siret'], 'user' => $user]);
            if ($existingCompany) {
                return ['error' => 'Company already exists'];
            }
            return $this->getCompanyBySiret($data['siret']);
        }

        return ['error' => 'SIREN or SIRET number is required'];
    }

    public function setCompanyData(Company $company, array $data): void
    {
        $company->setName($data['name']);
        $company->setAddress($data['address']);
        $company->setSiren($data['siren']);
        $company->setSiret($data['siret']);
        $company->setTvaNumber($data['tva_number']);
        $company->setStatus($data['status']);
    }
}