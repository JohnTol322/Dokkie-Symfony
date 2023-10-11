<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route(path: "/", name: "home_page", methods: ['GET'])]
    public function renderHome(): Response
    {
        return $this->render("default/home_page.html.twig");
    }

    #[Route(path: "/faq", name: "faq_page", methods: ['GET'])]
    public function renderFaq(): Response
    {
        return $this->render("default/faq_page.html.twig");
    }
}