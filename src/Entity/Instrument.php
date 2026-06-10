<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\InstrumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InstrumentRepository::class)]
#[ApiResource(
    operations: [new GetCollection(normalizationContext: ['groups' => ['instrument:read']])],
    normalizationContext: ['groups' => ['instrument:read']],
)]
class Instrument
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['instrument:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['instrument:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $model = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $serialNumber = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastCalibration = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $apiEndpoint = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $protocol = null;

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getModel(): ?string { return $this->model; }
    public function setModel(?string $model): static { $this->model = $model; return $this; }
    public function getSerialNumber(): ?string { return $this->serialNumber; }
    public function setSerialNumber(?string $serialNumber): static { $this->serialNumber = $serialNumber; return $this; }
    public function getLastCalibration(): ?\DateTimeImmutable { return $this->lastCalibration; }
    public function setLastCalibration(?\DateTimeImmutable $lastCalibration): static { $this->lastCalibration = $lastCalibration; return $this; }
    public function getApiEndpoint(): ?string { return $this->apiEndpoint; }
    public function setApiEndpoint(?string $apiEndpoint): static { $this->apiEndpoint = $apiEndpoint; return $this; }
    public function getProtocol(): ?string { return $this->protocol; }
    public function setProtocol(?string $protocol): static { $this->protocol = $protocol; return $this; }
}