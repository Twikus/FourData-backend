<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CompanyRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ApiResource(
    openapiContext: ['security' => [['JWT' => []]]],
    security: "is_granted('ROLE_USER')",
    operations: [
        new Get(
            name: 'api_companies_show',
            uriTemplate: '/companies/{id}',
            security: "is_granted('ROLE_USER')",
        ),
        new Post(
            name: 'api_companies_create',
            uriTemplate: '/companies',
            openapiContext: [
                'summary' => 'Register a new company',
                'description' => 'Register a new company',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => ['type' => 'string'],
                                    'siren' => ['type' => 'integer'],
                                    'siret' => ['type' => 'integer'],
                                ],
                            ],
                        ],
                    ],
                ],
                'responses' => [],
            ],
            security: "is_granted('ROLE_USER')",
        ),
        new Put(
            name: 'api_companies_update',
            uriTemplate: '/companies/{id}',
            security: "is_granted('ROLE_USER')",
        ),
        new Delete(
            name: 'api_companies_delete',
            uriTemplate: '/companies/{id}',
            security: "is_granted('ROLE_USER')",
        ),
    ]
)]
class Company
{
    // TODO : Security of the fields
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("company:read")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("company:read")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups("company:read")]
    private ?string $address = null;

    #[ORM\Column]
    #[Groups("company:read")]
    private ?int $siren = null;

    #[ORM\Column]
    #[Groups("company:read")]
    private ?int $siret = null;

    #[ORM\Column(length: 30)]
    #[Groups("company:read")]
    private ?string $tvaNumber = null;

    #[ORM\ManyToOne(inversedBy: 'companies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getSiren(): ?int
    {
        return $this->siren;
    }

    public function setSiren(int $siren): static
    {
        $this->siren = $siren;

        return $this;
    }

    public function getSiret(): ?int
    {
        return $this->siret;
    }

    public function setSiret(int $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getTvaNumber(): ?string
    {
        return $this->tvaNumber;
    }

    public function setTvaNumber(string $tvaNumber): static
    {
        $this->tvaNumber = $tvaNumber;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user;
    }

    public function setUserId(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
