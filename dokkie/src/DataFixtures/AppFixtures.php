<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\Payment;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $encoder,
        private readonly UserRepository              $userRepository,
        private readonly EventRepository             $eventRepository,
        private readonly ParticipantRepository       $participantRepository
    )
    {}

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadEvents($manager);
        $this->loadParticipants($manager);
        $this->loadPayments($manager);
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $manager->persist($this->createUser("joopie@test.nl"));
        $manager->persist($this->createUser("jaapie@test.nl"));
        $manager->persist($this->createUser("jantje@test.nl"));
        $manager->persist($this->createUser("nikkie@test.nl"));
        $manager->persist($this->createUser("appie@test.nl"));
        $manager->persist($this->createUser("richard@test.nl"));

        $manager->flush();
    }

    private function loadEvents(ObjectManager $manager): void
    {
        $manager->persist($this->createEvent("Bowling middag", "joopie@test.nl"));
        $manager->persist($this->createEvent("LAN Party", "jaapie@test.nl"));
        $manager->persist($this->createEvent("Verjaardagsfeest", "jaapie@test.nl"));
        $manager->persist($this->createEvent("Halloween", "appie@test.nl"));

        $manager->flush();
    }

    private function loadParticipants(ObjectManager $manager): void
    {
        $manager->persist($this->createParticipant("Bowling middag", "joopie@test.nl"));
        $manager->persist($this->createParticipant("Bowling middag", "jaapie@test.nl"));
        $manager->persist($this->createParticipant("Bowling middag", "jantje@test.nl"));
        $manager->persist($this->createParticipant("Bowling middag", "appie@test.nl"));
        $manager->persist($this->createParticipant("Bowling middag", "richard@test.nl"));

        $manager->persist($this->createParticipant("LAN Party", "joopie@test.nl"));
        $manager->persist($this->createParticipant("LAN Party", "jaapie@test.nl"));
        $manager->persist($this->createParticipant("LAN Party", "jantje@test.nl"));
        $manager->persist($this->createParticipant("LAN Party", "appie@test.nl"));
        $manager->persist($this->createParticipant("LAN Party", "richard@test.nl"));

        $manager->persist($this->createParticipant("Halloween", "joopie@test.nl"));
        $manager->persist($this->createParticipant("Halloween", "jaapie@test.nl"));
        $manager->persist($this->createParticipant("Halloween", "jantje@test.nl"));
        $manager->persist($this->createParticipant("Halloween", "appie@test.nl"));
        $manager->persist($this->createParticipant("Halloween", "richard@test.nl"));

        $manager->flush();
    }

    private function loadPayments(ObjectManager $manager): void
    {
        $manager->persist($this->createPayment("Vervoer", 23.65, "joopie@test.nl", "Bowling middag"));
        $manager->persist($this->createPayment("Baan huur", 27.50, "jaapie@test.nl", "Bowling middag"));
        $manager->persist($this->createPayment("Lunch", 54.85, "richard@test.nl", "Bowling middag"));
        $manager->persist($this->createPayment("Drankjes van te voren", 32.00, "appie@test.nl", "Bowling middag"));
        $manager->persist($this->createPayment("Drankjes tijdens bowlen", 44.35, "jantje@test.nl", "Bowling middag"));
        $manager->persist($this->createPayment("Fooi", 10, "jantje@test.nl", "Bowling middag"));
        $manager->persist($this->createPayment("Ijsjes", 7.50, "joopie@test.nl", "Bowling middag"));

        $manager->persist($this->createPayment("Snacks", 14.96, "jaapie@test.nl", "LAN Party"));
        $manager->persist($this->createPayment("Diner", 34.85, "richard@test.nl", "LAN Party"));
        $manager->persist($this->createPayment("Bier en Frisdrank", 56.50, "appie@test.nl", "LAN Party"));
        $manager->persist($this->createPayment("Games", 75, "jantje@test.nl", "LAN Party"));
        $manager->persist($this->createPayment("Apparatuur", 120, "joopie@test.nl", "LAN Party"));

        $manager->persist($this->createPayment("Kostuums", 65.75, "jaapie@test.nl", "Halloween"));
        $manager->persist($this->createPayment("Snoep", 18.65, "richard@test.nl", "Halloween"));
        $manager->persist($this->createPayment("Versiering", 12.50, "appie@test.nl", "Halloween"));

        $manager->flush();
    }

    private function createUser(string $email, string $password = "test"): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->encoder->hashPassword($user, $password));

        return $user;
    }

    private function createEvent(string $description, string $userEmail): Event
    {
        $event = new Event();
        $event->setDescription($description);
        $event->setActive(true);
        $event->setUser($this->userRepository->findOneBy(['email' => $userEmail]));
        $event->setDateCreated(new \DateTime());

        return $event;
    }

    private function createParticipant(string $eventName, string $userEmail): Participant
    {
        $participant = new Participant();
        $participant->setEvent($this->eventRepository->findOneBy(['description' => $eventName]));
        $participant->setUser($this->userRepository->findOneBy(['email' => $userEmail]));

        return $participant;
    }

    private function createPayment(string $description, float $amount, string $email, string $eventName): Payment
    {
        $payment = new Payment();
        $payment->setDescription($description);
        $payment->setAmount($amount);
        $payment->setDatePaid(new \DateTime());
        $user = $this->userRepository->findOneBy(['email' => $email]);
        $event = $this->eventRepository->findOneBy(['description' => $eventName]);
        $payment->setParticipant($this->participantRepository->findOneBy(['user' => $user, 'event' => $event]));
        $payment->setEvent($event);

        return $payment;
    }
}
