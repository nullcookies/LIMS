<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\SampleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SampleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['sample:read', 'sample:detail']]),
        new GetCollection(normalizationContext: ['groups' => ['sample:read']]),
        new Post(denormalizationContext: ['groups' => ['sample:write']]),
        new Patch(denormalizationContext: ['groups' => ['sample:write']]),
    ],
    normalizationContext: ['groups' => ['sample:read']],
    denormalizationContext: ['groups' => ['sample:write']],
)]
#[ApiFilter(SearchFilter::class, properties: ['barcode' => 'partial', 'status' => 'exact'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
class Sample
{
    const STATUS_REGISTERED = 'registered';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['sample:read', 'sample:detail'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 36, unique: true)]
    #[Groups(['sample:read', 'sample:detail', 'sample:write'])]
    private ?string $uuid = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['sample:read', 'sample:detail', 'sample:write'])]
    private ?string $barcode = null;

    #[ORM\Column(length: 50)]
    #[Groups(['sample:read', 'sample:detail'])]
    private string $status = self::STATUS_REGISTERED;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['sample:read', 'sample:detail', 'sample:write'])]
    private ?array $metadata = null;

    #[ORM\ManyToOne(inversedBy: 'samples')]
    #[Groups(['sample:read', 'sample:detail'])]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(inversedBy: 'samples')]
    #[Groups(['sample:read', 'sample:detail', 'sample:write'])]
    private ?SampleType $sampleType = null;

    #[ORM\OneToMany(targetEntity: SampleTest::class, mappedBy: 'sample', cascade: ['persist'])]
    #[Groups(['sample:detail'])]
    private Collection $sampleTests;

    #[ORM\OneToMany(targetEntity: SampleStatusHistory::class, mappedBy: 'sample')]
    private Collection $statusHistories;

    public function __construct()
    {
        $this->uuid = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->sampleTests = new ArrayCollection();
        $this->statusHistories = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void { $this->updatedAt = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }
    public function getUuid(): ?string { return $this->uuid; }
    public function setUuid(string $uuid): static { $this->uuid = $uuid; return $this; }
    public function getBarcode(): ?string { return $this->barcode; }
    public function setBarcode(string $barcode): static { $this->barcode = $barcode; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static
    {
        if (!in_array($status, [self::STATUS_REGISTERED, self::STATUS_IN_PROGRESS, self::STATUS_COMPLETED, self::STATUS_APPROVED, self::STATUS_REJECTED])) {
            throw new \InvalidArgumentException("Invalid status: $status");
        }
        $this->status = $status;
        return $this;
    }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function getMetadata(): ?array { return $this->metadata; }
    public function setMetadata(?array $metadata): static { $this->metadata = $metadata; return $this; }
    public function getCustomer(): ?Customer { return $this->customer; }
    public function setCustomer(?Customer $customer): static { $this->customer = $customer; return $this; }
    public function getSampleType(): ?SampleType { return $this->sampleType; }
    public function setSampleType(?SampleType $sampleType): static { $this->sampleType = $sampleType; return $this; }
    public function getSampleTests(): Collection { return $this->sampleTests; }
    public function getStatusHistories(): Collection { return $this->statusHistories; }
}