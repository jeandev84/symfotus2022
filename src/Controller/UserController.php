<?php
namespace App\Controller;

use App\Manager\UserManager;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
      private UserService $userService;


      public function __construct(UserService $userService)
      {
            $this->userService = $userService;
      }



      #[Route('/users', name: 'users_list', methods: ['GET'])]
      public function list(): Response
      {
           $user = $this->userService->create('My User');

           return $this->json($user->toArray());
      }
}