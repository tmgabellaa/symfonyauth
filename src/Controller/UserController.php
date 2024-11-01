<?php

namespace App\Controller;

use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $error_messages = null;
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['passwordConfirm'] ?? '';

            if(empty($name) || empty($email) || empty($password)) {
                $error_messages = 'Пустые поля';
            } elseif ($password !== $passwordConfirm){
                $error_messages = 'пароли не совпадают';
            } else{
                $userRepository->register($entityManager,$passwordHasher, $name, $email, $password);
            }
        }

        return $this->render('user/index.html.twig', [
            'error' => $error_messages
        ]);
    }
}
