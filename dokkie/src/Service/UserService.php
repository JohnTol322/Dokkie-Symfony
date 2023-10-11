<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $encoder
    ){}

    public function createUser(array $requestData): User
    {
        $user = new User();
        $user->setEmail($requestData['email']);
        $encodedPassword = $this->encoder->hashPassword($user, $requestData['password']);
        $user->setPassword($encodedPassword);

        return $this->userRepository->save($user);
    }
}