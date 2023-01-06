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



      #[Route('/users', name: 'users_list', methods: ['GET'])]
      public function list(): Response
      {
          $users = $this->userManager->findUsersByCriteria('J.R.R. Tolkien');

          return $this->json(array_map(static fn(User $user) => $user->toArray(), $users));
      }
}