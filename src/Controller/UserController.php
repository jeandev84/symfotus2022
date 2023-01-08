<?php
namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use App\Service\UserBuilderService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
      public function __construct(
          private UserManager $userManager,
          private UserBuilderService $userBuilderService,
          private LoggerInterface $logger
      )
      {
          $this->userBuilderService->setLogger($logger);
      }



      #[Route('/demo', name: 'demo', methods: ['GET'])]
      public function demo(): Response
      {
           $user = $this->userBuilderService->createUserWithTweets(
               'Charles Dickens',
               ['Oliver Twist', 'The Christmas Carol']
           );

           $userData = $this->userManager->findUserWithTweetsWithDBALQueryBuilder($user->getId());

           return $this->json($userData);
      }




      public function createAndFindUserWithTweetsWithDBALQueryBuilder()
      {
          $user = $this->userBuilderService->createUserWithTweets(
              'Charles Dickens',
              ['Oliver Twist', 'The Christmas Carol']
          );

          $userData = $this->userManager->findUserWithTweetsWithDBALQueryBuilder($user->getId());

          return $this->json($userData);
      }



      public function createAndFindUserWithTweetsWithQueryBuilder()
      {
          $user = $this->userBuilderService->createUserWithTweets(
              'Charles Dickens',
              ['Oliver Twist', 'The Christmas Carol']
          );

          $userData = $this->userManager->findUserWithTweetsWithQueryBuilder($user->getId());

          return $this->json($userData);
      }


      public function updateUserLoginViaDBALQueryBuilder()
      {
          $userId = 3;
          $user = $this->userManager->findUser($userId);
          $this->userManager->updateUserLoginWithDBALQueryBuilder($user->getId(), 'User is updated by DBAL');
          $this->userManager->clearEntityManager();
          $user = $this->userManager->findUser($userId);

          return $this->json($user->toArray());
      }



      public function updateUserLoginViaQueryBuilder()
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