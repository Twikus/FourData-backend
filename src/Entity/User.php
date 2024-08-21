<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;

use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

use App\Repository\UserRepository;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: false)]
#[ApiResource(
    formats: ['json'], 
    openapiContext: ['security' => [['JWT' => []]]],
    security: "is_granted('ROLE_USER')",
    operations: [
        new Get(
            name: 'api_users_me',
            uriTemplate: '/api/users/me',
            openapiContext: [
                'summary' => 'Get the current user',
                'description' => 'Get the current user',
            ],
            security: "is_granted('ROLE_USER')",
            securityMessage: 'Only authenticated users can access this resource.',
        ),
        new Post(
            name: 'api_register',
            uriTemplate: '/api/register',
            openapiContext: [
                'summary' => 'Register a new user',
                'description' => 'Register a new user',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'email' => ['type' => 'string'],
                                    'password' => ['type' => 'string'],
                                    'firstname' => ['type' => 'string'],
                                    'lastname' => ['type' => 'string'],
                                ],
                            ],
                        ],
                    ],
                ],
                'responses' => [
                    '201' => [
                        'description' => 'User created',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'token' => ['type' => 'string'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '400' => [
                        'description' => 'Invalid credentials',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'integer'],
                                        'message' => ['type' => 'string']
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '401' => [
                        'description' => 'Unauthorized <strong>(You should be disconnected)</strong>',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'integer'],
                                        'message' => ['type' => 'string']
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            security: "is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
            securityMessage: 'Only anonymous users can access this resource.',
        ),
        new Put(
            openapiContext: [
                'summary' => 'Update the current user',
                'description' => 'Update the current user',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'email' => ['type' => 'string'],
                                    'firstname' => ['type' => 'string'],
                                    'lastname' => ['type' => 'string'],
                                ],
                            ],
                        ],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'User updated',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'email' => ['type' => 'string'],
                                        'firstname' => ['type' => 'string'],
                                        'lastname' => ['type' => 'string'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '400' => [
                        'description' => 'Invalid credentials',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'integer'],
                                        'message' => ['type' => 'string']
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '403' => [
                        'description' => 'Forbidden <strong>(You can only update your own account)</strong>',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'integer'],
                                        'message' => ['type' => 'string']
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            extraProperties: [
                'standard_put' => true,
            ],
            security: "object == user",
            securityMessage: "You can only update your own account."
        ),
        new Delete(
            openapiContext: [
                'summary' => 'Delete the current user',
                'description' => 'Delete the current user',
                'responses' => [
                    '204' => [
                        'description' => 'User deleted',
                    ],
                    '403' => [
                        'description' => 'Forbidden <strong>(You can only delete your own account)</strong>',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => ['type' => 'integer'],
                                        'message' => ['type' => 'string']
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],
            security: "object == user",
            securityMessage: "You can only delete your own account."
        ),
        new GetCollection(
            name: 'api_users_companies',
            uriTemplate: '/api/users/{id}/companies',
            openapiContext: [
                'summary' => 'Get the companies of the user',
                'description' => 'Get the companies of the user',
            ],
        ),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("user:show")]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups("user:show")]
    private ?string $email = null;
    
    #[ORM\Column(length: 255)]
    #[Groups("user:show")]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups("user:show")]
    private ?string $lastname = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups("user:show")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Company>
     */
    #[ORM\OneToMany(targetEntity: Company::class, mappedBy: 'user')]
    #[Groups("user:show")]
    private Collection $companies;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): static
    {
        $this->email = $email;
    
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($role)
    {
        if (!in_array($role, $this->roles)) {
            array_push($this->roles, $role);
        }
 
        return $this;
    }
 
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }
 
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
 
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): static
    {
        if (!$this->companies->contains($company)) {
            $this->companies->add($company);
            $company->setUserId($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): static
    {
        if ($this->companies->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getUserId() === $this) {
                $company->setUserId(null);
            }
        }

        return $this;
    }
}
