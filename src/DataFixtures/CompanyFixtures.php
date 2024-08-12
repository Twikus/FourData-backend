<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Helper\CompanyHelper;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CompanyFixtures extends Fixture implements DependentFixtureInterface
{
    private CompanyHelper $companyHelper;

    public function __construct(CompanyHelper $companyHelper)
    {
        $this->companyHelper = $companyHelper;
    }

    public function load(ObjectManager $manager): void
    {
        $companies = [323782169,830368213,532085263];
        $user = $this->getReference('user_john_doe');

        $manager->persist($user);

        foreach ($companies as $siren) {
            $companyData = $this->companyHelper->getCompanyBySiren($siren);
            $company = new Company();
        
            $company->setUserId($user);
            $this->companyHelper->setCompanyData($company, $companyData);

            $manager->persist($company);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
