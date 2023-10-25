<?php
namespace App\Service;

use App\DTO\SaveUserDTO;
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

}