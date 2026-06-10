<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\QcSampleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: QcSampleRepository::class)]
#[ApiResource(
    operations: [new GetCollection(normalizationContext: ['groups' => ['qc:read']])],
    normalizationContext: ['groups' => ['qc:read']],
)]
class QcSample
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['qc:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private TestMethod $testMethod;

    #[ORM\Column(length: 50)]
    private ?string $controlType = null;

    #[ORM\Column(type: 'float')]
    private ?float $expectedValue = null;

    #[ORM\Column(type: 'float')]
    private ?float $measuredValue = null;

    #[ORM\Column]
    private \DateTimeImmutable $date;

    #[ORM\ManyToOne]
    private ?User $approvedBy = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    public function __construct() { $this->date = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }
    public function getTestMethod(): TestMethod { return $this->testMethod; }
    public function setTestMethod(TestMethod $testMethod): static { $this->testMethod = $testMethod; return $this; }
    public function getControlType(): ?string { return $this->controlType; }
    public function setControlType(string $controlType): static { $this->controlType = $controlType; return $this; }
    public function getExpectedValue(): ?float { return $this->expectedValue; }
    public function setExpectedValue(?float $expectedValue): static { $this->expectedValue = $expectedValue; return $this; }
    public function getMeasuredValue(): ?float { return $this->measuredValue; }
    public function setMeasuredValue(?float $measuredValue): static { $this->measuredValue = $measuredValue; return $this; }
    public function getDate(): \DateTimeImmutable { return $this->date; }
    public function setDate(\DateTimeImmutable $date): static { $this->date = $date; return $this; }
    public function getApprovedBy(): ?User { return $this->approvedBy; }
    public function setApprovedBy(?User $approvedBy): static { $this->approvedBy = $approvedBy; return $this; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
}