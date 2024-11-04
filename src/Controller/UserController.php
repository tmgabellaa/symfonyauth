<?php

namespace App\Controller;

use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{

    #[Route('/user', name: 'app_user')]
    public function register(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        Request $request
    ): Response
    {
        $error_messages = null;
        if($request->isMethod('POST')){

            $name = $request->request->get('name') ?? '';
            $email = $request->request->get('email') ?? '';
            $password = $request->request->get('password') ?? '';
            $passwordConfirm = $request->request->get('passwordConfirm') ?? '';

            if(empty($name) || empty($email) || empty($password)) {
                $error_messages = 'Пустые поля';
            } elseif ($password !== $passwordConfirm){
                $error_messages = 'пароли не совпадают';
            } else{
                $userRepository->register($entityManager,$passwordHasher, $name, $email, $password);
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('user/index.html.twig', [
            'error' => $error_messages
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(Request $request,UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository) : Response
    {
        $error_messages = null;

        if($request->isMethod('POST')){
            $email = $request->request->get('_username')?? '';
            $password = $request->request->get('_password')?? '';

            $result = $userRepository->login($email,$password,$passwordHasher);

            if($result){
                if(in_array('ROLE_ADMIN', $result->getRoles())){
                    return $this->redirectToRoute('app_admin');
                }elseif (in_array('ROLE_USER', $result->getRoles())){
                    echo 'user';
                }else{
                    $error_messages = 'у тебя нет прав ¯\_(ツ)_/¯';
                }
            } else {
                $error_messages = 'неверный логин или пароль';
            }
        }
        return $this->render('user/login.html.twig', [
            'error' => $error_messages
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {
        // Symfony автоматически обрабатывает выход
    }
}
