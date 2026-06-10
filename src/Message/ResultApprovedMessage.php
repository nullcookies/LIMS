<?php

namespace App\Message;

class ResultApprovedMessage
{
    public function __construct(
        private readonly int $sampleTestId,
        private readonly int $approvedByUserId,
    ) {}

    public function getSampleTestId(): int { return $this->sampleTestId; }
    public function getApprovedByUserId(): int { return $this->approvedByUserId; }
}