<?php

namespace App\Entity;

class ParticipantBalance
{
    private ?Participant $participant = null;

    private float $balance;

    private float $total = 0;

    private array $paymentPlan = [];

    /**
     * @return Participant|null
     */
    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    /**
     * @param Participant|null $participant
     */
    public function setParticipant(?Participant $participant): void
    {
        $this->participant = $participant;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function addPaymentPlan(string $plan): void
    {
        $this->paymentPlan[] = $plan;
    }

    public function getPaymentPlan(): array
    {
        return $this->paymentPlan;
    }

}
