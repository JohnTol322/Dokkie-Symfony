<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Entity\ParticipantBalance;
use App\Entity\Payment;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use App\Repository\PaymentRepository;
use DateTime;
use mysql_xdevapi\Result;

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

    public function calculatePaymentPlan(array $participantBalance): array
    {
        $results = [];
        $amounts = [];
        /** @var ParticipantBalance $balance */
        foreach ($participantBalance as $balance) {
            $amounts[$balance->getParticipant()->getUser()->getEmail()] = $balance->getBalance();
        }
        while(count(array_filter($amounts, function(float $balance){
            return $balance !== 0.0;
        })))
        {
            $highest = array_search(max($amounts),$amounts);
            $lowest = array_search(min($amounts),$amounts);

            if (abs($amounts[$highest]) < abs($amounts[$lowest])) {
                $amount = abs($amounts[$highest]);
                $amount = number_format( $amount, 2, '.', '' );
                $results[$highest][] = "{$highest} pays {$amount} EUR to {$lowest}";
                $amounts[$lowest] = $amounts[$lowest] + $amounts[$highest];
                $amounts[$highest] = 0;

            } else {
                $amount = abs($amounts[$lowest]);
                $amount = number_format( $amount, 2, '.', '' );
                $results[$highest][] = "{$highest} pays {$amount} EUR to {$lowest}";
                $amounts[$highest] = $amounts[$highest] - abs($amounts[$lowest]);
                $amounts[$lowest] = 0;
            }
        }

        /** @var ParticipantBalance $balance */
        foreach ($participantBalance as $balance) {
            $email = $balance->getParticipant()->getUser()->getEmail();
            if (array_key_exists($email, $results)) {
                foreach ($results[$email] as $plan) {
                    $balance->addPaymentPlan($plan);
                }
            }
        }

        return $participantBalance;
    }
}