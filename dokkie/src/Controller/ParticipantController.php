<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\EventService;
use App\Service\ParticipantService;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends AbstractController
{
    public function __construct(
        private readonly ParticipantService $participantService,
        private readonly EventService $eventService
    )
    {}

    #[Route(path: "/participant/{eventId}/create", name: "create_participant", methods: ['GET', 'POST'])]
    public function createParticipantAction(Request $request, int $eventId): Response
    {
        $event = null;

        try {

            $event = $this->eventService->getEvent($eventId);

            if ($request->getMethod() === "POST") {
                $request->request->add(["event" => $eventId]);
                $participant = $this->participantService->createParticipant($request->request->all());
                $this->addFlash("success", "{$participant->getUser()->getEmail()} added to {$participant->getEvent()->getDescription()}");
            }
        } catch (\Exception $e) {
            $this->addFlash("warning", $e->getMessage());
        } finally {
            return $this->render("participant/create_participant.html.twig", [
                "participantList" => $this->participantService->getParticipantsByEvent($eventId),
                "event" => $event
            ]);
        }
    }

    #[Route(path: "/participant/{participantId}/event/{eventId}/delete/", name: "delete_participant", methods: ['GET'])]
    public function deleteParticipantAction(int $participantId, int $eventId): Response
    {
        try {
            $this->participantService->deleteParticipant($participantId);
        } catch (\Exception $e) {
            $this->addFlash("danger", $e->getMessage());
        } finally {
            return $this->redirectToRoute("get_event", ['id' => $eventId]);
        }
    }
}