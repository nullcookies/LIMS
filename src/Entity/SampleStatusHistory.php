<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class SampleStatusHistory
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['sample:detail'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'statusHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private Sample $sample;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['sample:detail'])]
    private ?string $fromStatus = null;

    #[ORM\Column(length: 50)]
    #[Groups(['sample:detail'])]
    private string $toStatus;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $changedBy;

    #[ORM\Column]
    #[Groups(['sample:detail'])]
    private \DateTimeImmutable $createdAt;

    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }
    public function getSample(): Sample { return $this->sample; }
    public function setSample(Sample $sample): static { $this->sample = $sample; return $this; }
    public function getFromStatus(): ?string { return $this->fromStatus; }
    public function setFromStatus(?string $fromStatus): static { $this->fromStatus = $fromStatus; return $this; }
    public function getToStatus(): string { return $this->toStatus; }
    public function setToStatus(string $toStatus): static { $this->toStatus = $toStatus; return $this; }
    public function getChangedBy(): User { return $this->changedBy; }
    public function setChangedBy(User $changedBy): static { $this->changedBy = $changedBy; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}