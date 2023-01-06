<?php
namespace App\Service;

use App\Entity\Tweet;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{

      private EntityManagerInterface $entityManager;


      public function __construct(EntityManagerInterface $entityManager)
      {
           $this->entityManager = $entityManager;
      }


      /**
       * @param string $login
       * @return User
      */
      public function create(string $login): User
      {
           $user = new User();
           $user->setLogin($login);
           $user->setCreatedAt();
           $user->setUpdatedAt();

           $this->entityManager->persist($user);
           $this->entityManager->flush();

           return $user;
      }




      /**
       * @param User $author
       * @param string $text
       * @return void
      */
      public function postTweet(User $author, string $text): void
      {
           $tweet = new Tweet();
           $tweet->setAuthor($author);
           $tweet->setText($text);
           $tweet->setCreatedAt();
           $tweet->setUpdatedAt();
           $author->addTweet($tweet);
           $this->entityManager->persist($tweet);
           $this->entityManager->flush();
      }


     public function clearEntityManager()
     {
          $this->entityManager->clear();
     }

     /**
      * @param int $id
      * @return User|null
     */
     public function findUser(int $id): ?User
     {
          $repository = $this->entityManager->getRepository(User::class);
          $user       = $repository->find($id);

          return $user instanceof User ? $user : null;
     }
}