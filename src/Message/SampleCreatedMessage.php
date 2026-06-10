<?php

namespace App\Message;

class SampleCreatedMessage
{
    public function __construct(
        private readonly int $sampleId,
        private readonly string $barcode,
    ) {}

    public function getSampleId(): int { return $this->sampleId; }
    public function getBarcode(): string { return $this->barcode; }
}