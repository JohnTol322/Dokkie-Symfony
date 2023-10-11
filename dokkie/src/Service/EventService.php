<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Repository\EventRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\SecurityBundle\Security;

class EventService
{
    public function __construct(
        private readonly EventRepository $eventRepository,
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
        return $this->eventRepository->findOneBy(["id" => $eventId]);
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
}