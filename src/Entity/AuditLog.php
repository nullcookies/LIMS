<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AuditLog
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column(length: 100)]
    private string $entityType;

    #[ORM\Column(length: 36)]
    private string $entityId;

    #[ORM\Column(length: 50)]
    private string $action;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $oldValue = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $newValue = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $ip = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
    public function getEntityType(): string { return $this->entityType; }
    public function setEntityType(string $entityType): static { $this->entityType = $entityType; return $this; }
    public function getEntityId(): string { return $this->entityId; }
    public function setEntityId(string $entityId): static { $this->entityId = $entityId; return $this; }
    public function getAction(): string { return $this->action; }
    public function setAction(string $action): static { $this->action = $action; return $this; }
    public function getOldValue(): ?array { return $this->oldValue; }
    public function setOldValue(?array $oldValue): static { $this->oldValue = $oldValue; return $this; }
    public function getNewValue(): ?array { return $this->newValue; }
    public function setNewValue(?array $newValue): static { $this->newValue = $newValue; return $this; }
    public function getIp(): ?string { return $this->ip; }
    public function setIp(?string $ip): static { $this->ip = $ip; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}