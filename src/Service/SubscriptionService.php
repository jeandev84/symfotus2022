<?php
namespace App\Service;

use App\DTO\AddFollowersDTO;
use App\DTO\SaveUserDTO;
use App\Entity\Subscription;
use App\Entity\User;
use App\Manager\SubscriptionManager;
use App\Manager\UserManager;

class SubscriptionService
{

    public function __construct(
        private UserManager $userManager,
        private SubscriptionManager $subscriptionManager
    )
    {
    }



    public function subscribe(int $authorId, int $followerId): bool
    {
        $author = $this->userManager->getUserById($authorId);
        if (!($author instanceof User)) {
            return false;
        }
        $follower = $this->userManager->getUserById($followerId);
        if (!($follower instanceof User)) {
            return false;
        }

        $this->subscriptionManager->addSubscription($author, $follower);

        return true;
    }


    /**
     * Позволяет создавать автор с многими подписчиков
     * @param User $user
     * @param string $followerLogin
     * @param int $count
     * @return int
     */
    public function addFollowers(User $user, string $followerLogin, int $count): int
    {
        $createdFollowers = 0;

        for ($i = 0; $i < $count; $i++) {

            $login = "{$followerLogin}_#$i";
            $password = $followerLogin;
            $age = $i;
            $isActive = true;

            $data = compact('login', 'password', 'age', 'isActive');
            $followerId = $this->userManager->saveUserFromDTO(new User(), new SaveUserDTO($data));

            if ($followerId !== null) {
                $this->subscribe($user->getId(), $followerId);
                $createdFollowers++;
            }
        }

        return $createdFollowers;
    }


    /**
     * @return string[]
     *
     * @throws \JsonException
     */
    public function getFollowersMessages(User $user, string $followerLogin, int $count): array
    {
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = (new AddFollowersDTO($user->getId(), "$followerLogin #$i", 1))->toAMQPMessage();
        }

        return $result;
    }

//    /**
//     * @return int[]
//     */
//    public function getFollowerIds(int $authorId): array
//    {
//        $subscriptions = $this->getSubscriptionsByAuthorId($authorId);
//        $mapper = static function(Subscription $subscription) {
//            return $subscription->getFollower()->getId();
//        };
//
//        return array_map($mapper, $subscriptions);
//    }
//
//    /**
//     * @return Subscription[]
//     */
//    private function getSubscriptionsByAuthorId(int $authorId): array
//    {
//        $author = $this->userManager->getUserById($authorId);
//        if (!($author instanceof User)) {
//            return [];
//        }
//        return $this->subscriptionManager->findAllByAuthor($author);
//    }
//
//    /**
//     * @return int[]
//     */
//    public function getAuthorIds(int $followerId): array
//    {
//        $subscriptions = $this->getSubscriptionsByFollowerId($followerId);
//        $mapper = static function(Subscription $subscription) {
//            return $subscription->getAuthor()->getId();
//        };
//
//        return array_map($mapper, $subscriptions);
//    }
//
//    /**
//     * @return Subscription[]
//     */
//    private function getSubscriptionsByFollowerId(int $followerId): array
//    {
//        $follower = $this->userManager->getUserById($followerId);
//        if (!($follower instanceof User)) {
//            return [];
//        }
//        return $this->subscriptionManager->findAllByFollower($follower);
//    }
//
//
//    /**
//     * @return User[]
//     */
//    public function getFollowers(int $authorId): array
//    {
//        $subscriptions = $this->getSubscriptionsByAuthorId($authorId);
//        $mapper = static function(Subscription $subscription) {
//            return $subscription->getFollower();
//        };
//
//        return array_map($mapper, $subscriptions);
//    }

}