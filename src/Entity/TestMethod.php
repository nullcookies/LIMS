<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\TestMethodRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TestMethodRepository::class)]
#[ApiResource(
    operations: [new GetCollection(normalizationContext: ['groups' => ['test_method:read']])],
    normalizationContext: ['groups' => ['test_method:read']],
)]
class TestMethod
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['test_method:read', 'sample_test:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['test_method:read', 'sample_test:read'])]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Groups(['test_method:read', 'sample_test:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $unit = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $price = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $normMin = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $normMax = null;

    public function getId(): ?int { return $this->id; }
    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getUnit(): ?string { return $this->unit; }
    public function setUnit(?string $unit): static { $this->unit = $unit; return $this; }
    public function getPrice(): ?float { return $this->price; }
    public function setPrice(?float $price): static { $this->price = $price; return $this; }
    public function getNormMin(): ?float { return $this->normMin; }
    public function setNormMin(?float $normMin): static { $this->normMin = $normMin; return $this; }
    public function getNormMax(): ?float { return $this->normMax; }
    public function setNormMax(?float $normMax): static { $this->normMax = $normMax; return $this; }
}