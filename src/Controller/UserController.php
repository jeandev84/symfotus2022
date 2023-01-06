<?php
namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
      private UserManager $userManager;

      public function __construct(UserManager $userManager)
      {
          $this->userManager = $userManager;
      }



      #[Route('/demo', name: 'demo', methods: ['GET'])]
      public function demo(): Response
      {
          $user = $this->userManager->create('J.R.R. Tolkien');

          return $this->json($user->toArray());
      }




      #[Route('/user/create', name: 'user_create', methods: ['POST'])]
      public function create(): Response
      {
          $user = $this->userManager->create('J.R.R. Tolkien');

          return $this->json($user->toArray());
      }



      #[Route('/users', name: 'users_list', methods: ['GET'])]
      public function list(): Response
      {
          $users = $this->userManager->findUsersByCriteria('J.R.R. Tolkien');

          return $this->json(array_map(static fn(User $user) => $user->toArray(), $users));
      }
}