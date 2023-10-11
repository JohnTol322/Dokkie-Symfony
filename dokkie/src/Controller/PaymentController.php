<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\ParticipantService;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly ParticipantService $participantService
    )
    {}

    #[Route(path: "/payment/{eventId}/create", name: "create_payment", methods: ['GET', 'POST'])]
    public function createPaymentAction(Request $request, int $eventId): Response
    {
        try {
            $participantList = $this->participantService->getParticipantsByEvent($eventId);

            if ($request->getMethod() === "POST") {

                $request->request->add(['event' => $eventId]);
                $payment = $this->paymentService->createPayment($request->request->all());

                $this->addFlash("success", "Payment added to event: {$payment->getEvent()->getDescription()}");

                return $this->redirectToRoute("get_event", ['id' => $eventId]);
            }
            return $this->render("payment/create_payment.html.twig", ['participantList' => $participantList]);
        } catch (\Exception $e) {
            $this->addFlash( "danger", $e->getMessage());
            return $this->render("payment/create_payment.html.twig", ['participantList' => []]);
        }
    }
}