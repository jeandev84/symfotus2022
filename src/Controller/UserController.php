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
           $author = $this->userService->create('J.R.R Tolkien');
           $this->userService->postTweet($author, 'The Load of the Rings');
           $this->userService->postTweet($author, 'The Hobbit');

           $authorId = $author->getId();
           $this->userService->clearEntityManager();

           $author = $this->userService->findUser($authorId);

           return $this->json($author->toArray());
      }


      public function addTweetUsingClearEntityManager()
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