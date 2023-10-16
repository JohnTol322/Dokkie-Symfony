<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Entity\ParticipantBalance;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use App\Repository\PaymentRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\SecurityBundle\Security;

class EventService
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly ParticipantRepository $participantRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly Security $security
    ){}

    public function createEvent(array $requestData): Event
    {
        $event = new Event();

        $event->setDescription($requestData['description']);
        $event->setUser($this->security->getUser());
        $event->setActive(true);
        $event->setDateCreated(new \DateTime());

        return $this->eventRepository->save($event);
    }

    public function getEvents(): array
    {
        return $this->eventRepository->findBy(['user' => $this->security->getUser()]);
    }

    public function getEvent(int $eventId): Event
    {
        $event = $this->eventRepository->findOneBy(["id" => $eventId]);
        if ($event === null) {
            throw new \Exception("Event could not be found");
        }

        return $event;
    }

    public function updateEvent(array $requestData): Event
    {
        $event = $this->eventRepository->findOneBy(['id' => $requestData['id']]);
        if ($event === null) {
            throw new \Exception("Event does not exist");
        }

        $event->setDescription($requestData['description']);

        return $this->eventRepository->save($event);
    }

    public function deleteEvent(int $eventId): bool
    {
        $event = $this->eventRepository->findOneBy(['id' => $eventId]);
        if ($event === null) {
            throw new \Exception("Event does not exist");
        }
        return $this->eventRepository->remove($event);
    }

    public function getTotalPaymentAmountByEvent(int $eventId): float
    {
        $totalAmount = 0;
        $payments = $this->paymentRepository->findBy(['event' => $this->getEvent($eventId)]);
        foreach ($payments as $payment) {
            $totalAmount += $payment->getAmount();
        }

        return $totalAmount;
    }

    public function calculateParticipantBalance(int $eventId): array
    {
        $participantBalances = [];
        $participants = $this->participantRepository->findBy(['event' => $this->getEvent($eventId)]);

        foreach ($participants as $participant) {
            $balance = new ParticipantBalance();
            foreach($participant->getPayments() as $payment) {
                $balance->setParticipant($participant);
                $balance->setTotal($balance->getTotal()+$payment->getAmount());
                $balance->setBalance(($this->getTotalPaymentAmountByEvent($eventId) / count($participants)) - $balance->getTotal());
            }
            $participantBalances[] = $balance;
        }

        return $participantBalances;
    }
}