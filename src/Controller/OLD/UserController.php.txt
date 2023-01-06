<?php
namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use App\Service\UserBuilderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
      private UserBuilderService $userService;


      public function __construct(UserBuilderService $userService)
      {
            $this->userService = $userService;
      }



      #[Route('/users', name: 'users_list', methods: ['GET'])]
      public function list(): Response
      {
           $users = $this->userService->findUsersByCriteria('Tolkien');

           return $this->json(array_map(static fn(User $user) => $user->toArray(), $users));
      }
}