<?php
namespace App\Service;

use App\Entity\Tweet;
use App\Entity\User;
use App\Entity\Subscription;
use Doctrine\Common\Collections\Criteria;
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


     /**
      * @param string $login
      * @return array|object[]
     */
     public function findUsersByLogin(string $login)
     {
           $repository = $this->entityManager->getRepository(User::class);

           return $repository->findBy(['login' => $login, 'id' => 10]);
     }


     /**
      * @param string $login
      * @return array|mixed[]
     */
     public function findUsersByCriteria(string $login)
     {
           $criteria = Criteria::create();

           /** @noinspection NullPointerExceptionInspection */
           $criteria->andWhere(Criteria::expr()->contains('login', $login));

           /** @noinspection NullPointerExceptionInspection */
           $criteria->andWhere(Criteria::expr()->lte('id', 10));
           # $criteria->andWhere(Criteria::expr()->eq('login', $login));

           $repository = $this->entityManager->getRepository(User::class);

           return $repository->matching($criteria)->toArray();
     }




    /**
     * @param User $author
     * @param User $follower
     * @return void
    */
    public function subscribeUser(User $author, User $follower): void
    {
         $author->addFollower($follower);
         $follower->addAuthor($author);
         $this->entityManager->flush();
    }



    public function addSubscription(User $author, User $follower): void
    {
        $subscription = new Subscription();
        $subscription->setAuthor($author);
        $subscription->setFollower($follower);
        $subscription->setCreatedAt();
        $subscription->setUpdatedAt();
        $author->addSubscriptionFollower($subscription);
        $follower->addSubscriptionAuthor($subscription);
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }
}