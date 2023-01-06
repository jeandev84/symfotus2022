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
           /*
           $user = $this->userManager->findUser(3);
           $this->userManager->updateUserLoginWithQueryBuilder($user->getId(), 'User is updated');
           */

           $userId = 3;
           $user = $this->userManager->findUser($userId);
           $this->userManager->updateUserLoginWithQueryBuilder($user->getId(), 'User is updated twice');
           $this->userManager->clearEntityManager();
           $user = $this->userManager->findUser($userId);

           return $this->json($user->toArray());
      }





      public function updateUserLogin()
      {
          $userId = 3;
          $user = $this->userManager->findUser($userId);
          $this->userManager->updateUserLoginWithQueryBuilder($user->getId(), 'User is updated twice');
          $this->userManager->clearEntityManager();
          $user = $this->userManager->findUser($userId);

          return $this->json($user->toArray());
      }




      public function searchUsersByLogin()
      {
          $users = $this->userManager->findUsersWithQueryBuilder('Lewis');

          return $this->json(array_map(static fn(User $user) => $user->toArray(), $users));
      }




      public function createUserTestingGedmoTimestampable()
      {
           $user = $this->userManager->create('Terry Pratchett');
           sleep(1);

           $this->userManager->updateUserLogin($user->getId(), 'Lewis Carroll');

           return $this->json($user->toArray());
      }


      #[Route('/user/{id}', name: 'user.update', methods: ['PUT'])]
      public function update()
      {
          $user = $this->userManager->updateUserLogin(2, 'UserDemoUpdated');

          [$data, $code] = $user === null ? [null, Response::HTTP_NOT_FOUND] : [$user->toArray(), Response::HTTP_OK];

          return $this->json($data, $code);
      }




      #[Route('/user/create', name: 'user.create', methods: ['POST'])]
      public function create(): Response
      {
          $user = $this->userManager->create('J.R.R. Tolkien');

          return $this->json($user->toArray());
      }



      #[Route('/users', name: 'users.list', methods: ['GET'])]
      public function list(): Response
      {
          $users = $this->userManager->findUsersByCriteria('J.R.R. Tolkien');

          return $this->json(array_map(static fn(User $user) => $user->toArray(), $users));
      }
}