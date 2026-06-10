<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\SampleTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SampleTypeRepository::class)]
#[ApiResource(
    operations: [new GetCollection(normalizationContext: ['groups' => ['sample_type:read']])],
    normalizationContext: ['groups' => ['sample_type:read']],
)]
class SampleType
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['sample_type:read', 'sample:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['sample_type:read', 'sample:read', 'sample:write'])]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Sample::class, mappedBy: 'sampleType')]
    private Collection $samples;

    public function __construct() { $this->samples = new ArrayCollection(); }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getSamples(): Collection { return $this->samples; }
}