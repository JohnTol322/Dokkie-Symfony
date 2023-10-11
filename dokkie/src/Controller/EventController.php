<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\EventService;
use App\Service\ParticipantService;
use App\Service\PaymentService;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    public function __construct(
        private readonly EventService $eventService,
        private readonly PaymentService $paymentService,
        private readonly ParticipantService $participantService
    )
    {}

    #[Route(path: "/event/create", name: "create_event", methods: ['POST', 'GET'])]
    public function createEventAction(Request $request): Response
    {
        try {
            if ($request->getMethod() === "POST") {
                $event = $this->eventService->createEvent($request->request->all());
                $this->addFlash("success", "Event is created");
                return $this->redirectToRoute("create_participant", ["eventId" => $event->getId()]);
            }
            return $this->render("event/create_event.html.twig");
        } catch (\Exception $e) {
            $this->addFlash("warning", $e->getMessage());
            return $this->render("event/create_event.html.twig");
        }
    }

    #[Route(path: "/events", name: "list_events", methods: ['GET'])]
    public function listEventsAction(): Response
    {
        $events = [];
        try {
            $events = $this->eventService->getEvents();
        } catch (\Exception $e) {
            $this->addFlash("danger", $e->getMessage());
        } finally {
            return $this->render("event/list_events.html.twig", ["events" => $events]);
        }
    }

    #[Route(path: "/event/{id}/details", name: "get_event", methods: ['GET'])]
    public function getEvent(int $id): Response
    {
        $event = null;
        $paymentList = [];
        $participantList= [];
        try {
            $event = $this->eventService->getEvent($id);
            $paymentList = $this->paymentService->getPaymentsByEvent($id);
            $participantList = $this->participantService->getParticipantsByEvent($id);
        } catch (\Exception $e) {
            $this->addFlash("danger", $e->getMessage());
        } finally {
            return $this->render("event/show_event.html.twig", [
                "event" => $event,
                "paymentList" => $paymentList,
                "participantList" => $participantList
            ]);
        }
    }

    #[Route(path: "/event/{id}/edit", name: "edit_event", methods: ['GET', 'POST'])]
    public function editEvent(Request $request, int $id): Response
    {
        try {
            if ($request->getMethod() === "POST") {
                $event = $this->eventService->updateEvent($request->request->all());
                $this->addFlash("success", "Event is updated");
                return $this->redirectToRoute("get_event", ["id" => $event->getId()]);
            } else {
                $event = $this->eventService->getEvent($id);
                return $this->render("event/edit_event.html.twig", ["event" => $event]);
            }
        } catch (\Exception $e) {
            $this->addFlash("danger", $e->getMessage());
            return $this->render("event/edit_event.html.twig", ["event" => null]);
        }
    }

    #[Route(path: "/event/{id}/delete", name: "delete_event", methods: ['DELETE', 'GET'])]
    public function deleteEvent(int $id): Response
    {
        try {
            $this->eventService->deleteEvent($id);
            $this->addFlash("warning", "Deletion was successful");
        } catch (\Exception $e) {
            $this->addFlash("danger", $e->getMessage());
        } finally {
            return $this->redirectToRoute("list_events");
        }
    }
}