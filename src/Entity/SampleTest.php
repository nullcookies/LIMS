<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\SampleTestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SampleTestRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['sample_test:read']]),
        new Post(denormalizationContext: ['groups' => ['sample_test:write']]),
        new Patch(denormalizationContext: ['groups' => ['sample_test:write']]),
    ],
    normalizationContext: ['groups' => ['sample_test:read']],
    denormalizationContext: ['groups' => ['sample_test:write']],
)]
class SampleTest
{
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['sample_test:read', 'sample:detail'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sampleTests')]
    #[ORM\JoinColumn(nullable: false)]
    private Sample $sample;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['sample_test:read', 'sample_test:write', 'sample:detail'])]
    private TestMethod $testMethod;

    #[ORM\Column(length: 50)]
    #[Groups(['sample_test:read', 'sample:detail'])]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['sample_test:read', 'sample_test:write'])]
    private ?string $resultValue = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['sample_test:read', 'sample_test:write'])]
    private ?string $resultText = null;

    #[ORM\ManyToOne]
    #[Groups(['sample_test:read'])]
    private ?User $approvedBy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $approvedAt = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['sample_test:read', 'sample_test:write'])]
    private ?float $uncertainty = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['sample_test:read'])]
    private ?string $qcStatus = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void { $this->updatedAt = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }
    public function getSample(): Sample { return $this->sample; }
    public function setSample(Sample $sample): static { $this->sample = $sample; return $this; }
    public function getTestMethod(): TestMethod { return $this->testMethod; }
    public function setTestMethod(TestMethod $testMethod): static { $this->testMethod = $testMethod; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
    public function getResultValue(): ?string { return $this->resultValue; }
    public function setResultValue(?string $resultValue): static { $this->resultValue = $resultValue; return $this; }
    public function getResultText(): ?string { return $this->resultText; }
    public function setResultText(?string $resultText): static { $this->resultText = $resultText; return $this; }
    public function getApprovedBy(): ?User { return $this->approvedBy; }
    public function setApprovedBy(?User $approvedBy): static { $this->approvedBy = $approvedBy; return $this; }
    public function getApprovedAt(): ?\DateTimeImmutable { return $this->approvedAt; }
    public function setApprovedAt(?\DateTimeImmutable $approvedAt): static { $this->approvedAt = $approvedAt; return $this; }
    public function getUncertainty(): ?float { return $this->uncertainty; }
    public function setUncertainty(?float $uncertainty): static { $this->uncertainty = $uncertainty; return $this; }
    public function getQcStatus(): ?string { return $this->qcStatus; }
    public function setQcStatus(?string $qcStatus): static { $this->qcStatus = $qcStatus; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}