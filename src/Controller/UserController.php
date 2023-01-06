<?php
namespace App\Controller;

use App\Entity\User;
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
           /* $users = $this->userService->findUsersByLogin('Ivan Ivanov'); */

           $users = $this->userService->findUsersByCriteria('Tolkien');

           return $this->json(array_map(static fn(User $user) => $user->toArray(), $users));
      }





      private function addSubscription()
      {
          $author   = $this->userService->create('J.R.R Tolkien');
          $follower = $this->userService->create('Ivan Ivanov');
          $this->userService->subscribeUser($author, $follower);
          $this->userService->addSubscription($author, $follower);

          return $this->json([$author->toArray(), $follower->toArray()]);
      }




      private function addPostTweet()
      {
          $author = $this->userService->create('J.R.R Tolkien');
          $this->userService->postTweet($author, 'The Load of the Rings');
          $this->userService->postTweet($author, 'The Hobbit');

          return $this->json($author->toArray());
      }





      // NOT RECOMMENDED
      private function addTweetUsingClearEntityManager()
      {
          $author = $this->userService->create('J.R.R Tolkien');
          $this->userService->postTweet($author, 'The Load of the Rings');
          $this->userService->postTweet($author, 'The Hobbit');

          $authorId = $author->getId();
          $this->userService->clearEntityManager();

          $author = $this->userService->findUser($authorId);

          return $this->json($author->toArray());
      }
}