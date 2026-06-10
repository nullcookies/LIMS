<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['customer:read']]),
        new Post(denormalizationContext: ['groups' => ['customer:write']]),
    ],
    normalizationContext: ['groups' => ['customer:read']],
    denormalizationContext: ['groups' => ['customer:write']],
)]
class Customer
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['customer:read', 'sample:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['customer:read', 'customer:write', 'sample:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 12, nullable: true)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?string $inn = null;

    #[ORM\Column(length: 9, nullable: true)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?string $kpp = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?string $contactPerson = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['customer:read'])]
    private ?int $b24Id = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(targetEntity: Sample::class, mappedBy: 'customer')]
    private Collection $samples;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->samples = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getInn(): ?string { return $this->inn; }
    public function setInn(?string $inn): static { $this->inn = $inn; return $this; }
    public function getKpp(): ?string { return $this->kpp; }
    public function setKpp(?string $kpp): static { $this->kpp = $kpp; return $this; }
    public function getContactPerson(): ?string { return $this->contactPerson; }
    public function setContactPerson(?string $contactPerson): static { $this->contactPerson = $contactPerson; return $this; }
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): static { $this->phone = $phone; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): static { $this->email = $email; return $this; }
    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): static { $this->address = $address; return $this; }
    public function getB24Id(): ?int { return $this->b24Id; }
    public function setB24Id(?int $b24Id): static { $this->b24Id = $b24Id; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function getSamples(): Collection { return $this->samples; }
}