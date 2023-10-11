<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Entity\Payment;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use App\Repository\PaymentRepository;
use DateTime;

class PaymentService
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly ParticipantRepository $participantRepository,
        private readonly EventRepository $eventRepository
    )
    {}

    public function createPayment(array $requestData): Payment
    {
        $payment = new Payment();

        $event = $this->eventRepository->findOneBy(['id' => $requestData['event']]);
        if ($event === null) {
            throw new \Exception("Event does not exist");
        }
        $payment->setEvent($event);
        $participant = $this->participantRepository->findOneBy(['id' => $requestData['participant']]);
        if ($participant === null) {
            throw new \Exception("Participant not found");
        }
        $payment->setParticipant($participant);

        $payment->setAmount((float)$requestData['amount']);
        $payment->setDescription($requestData['description']);
        $payment->setDatePaid(new DateTime((string)$requestData['datePaid']));

        return $this->paymentRepository->save($payment);
    }

    public function getPaymentsByEvent(int $eventId): array
    {
        $event = $this->eventRepository->findOneBy(['id' => $eventId]);
        return $this->paymentRepository->findBy(['event' => $event]);
    }
}