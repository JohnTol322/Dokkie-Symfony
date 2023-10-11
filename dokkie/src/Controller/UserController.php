<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(private readonly UserService $userService)
    {}

    #[Route(path: "/user/create", name: "create_user", methods: ['POST', 'GET'])]
    public function createUserAction(Request $request): Response
    {
        try {
            $user = null;
            if ($request->getMethod() === "POST") {
                $user = $this->userService->createUser($request->request->all());
                $this->addFlash("success", "Welcome to Dokkie, {$user->getEmail()}");
            }
        } catch (\Exception $e) {
            $this->addFlash("danger", $e->getMessage());
        } finally {
            return $this->render("user/create_user.html.twig", ["user" => $user]);
        }
    }
}