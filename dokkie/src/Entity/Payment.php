<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datePaid = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $participant = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\OneToMany(mappedBy: 'payment', targetEntity: PaymentException::class)]
    private Collection $paymentExceptions;

    public function __construct()
    {
        $this->paymentExceptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDatePaid(): ?\DateTimeInterface
    {
        return $this->datePaid;
    }

    public function setDatePaid(\DateTimeInterface $datePaid): static
    {
        $this->datePaid = $datePaid;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): static
    {
        $this->participant = $participant;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Collection<int, PaymentException>
     */
    public function getPaymentExceptions(): Collection
    {
        return $this->paymentExceptions;
    }

    public function addPaymentException(PaymentException $paymentException): static
    {
        if (!$this->paymentExceptions->contains($paymentException)) {
            $this->paymentExceptions->add($paymentException);
            $paymentException->setPayment($this);
        }

        return $this;
    }

    public function removePaymentException(PaymentException $paymentException): static
    {
        if ($this->paymentExceptions->removeElement($paymentException)) {
            // set the owning side to null (unless already changed)
            if ($paymentException->getPayment() === $this) {
                $paymentException->setPayment(null);
            }
        }

        return $this;
    }
}
