<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Participant;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use App\Repository\PaymentRepository;
use App\Repository\UserRepository;

class ParticipantService
{
    public function __construct(
        private readonly ParticipantRepository $participantRepository,
        private readonly EventRepository       $eventRepository,
        private readonly UserRepository        $userRepository
    ){}

    public function createParticipant(array $requestData): Participant
    {
        $participant = new Participant();
        $user = $this->userRepository->findOneBy(['email' => $requestData['email']]);
        if ($user === null) {
            throw new \Exception("User does not exist");
        }
        $participant->setUser($user);

        $event = $this->eventRepository->findOneBy(['id' => $requestData['event']]);
        if ($event === null) {
            throw new \Exception("Event does not exist");
        }
        $participant->setEvent($event);

        $participantExists = $this->participantRepository->findOneBy(['user' => $user, 'event' => $event]);
        if ($participantExists) {
            throw new \Exception("Participant already exists for this event.");
        }

        return $this->participantRepository->save($participant);
    }

    public function getParticipantsByEvent(int $eventId): array
    {
        $event = $this->eventRepository->findOneBy(['id' => $eventId]);
        return $this->participantRepository->findBy(['event' => $event]);
    }

    public function deleteParticipant(int $participantId): bool
    {
        $participant = $this->participantRepository->findOneBy(['id' => $participantId]);
        if ($participant === null) {
            throw new \Exception("Participant could not be found");
        }

        return $this->participantRepository->remove($participant);
    }
}