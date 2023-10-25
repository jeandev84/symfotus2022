<?php
namespace App\Controller\Api\AddFollowers\v1;


use App\DTO\AddFollowersDTO;
use App\Manager\UserManager;
use App\Service\AsyncService;
use App\Service\SubscriptionService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class Controller extends AbstractFOSRestController
{

     private SubscriptionService $subscriptionService;
     private UserManager $userManager;
     private AsyncService $asyncService;

     public function __construct(
         SubscriptionService $subscriptionService,
         UserManager $userManager,
         AsyncService $asyncService
     )
     {
         $this->subscriptionService = $subscriptionService;
         $this->userManager = $userManager;
         $this->asyncService = $asyncService;
     }



     #[Rest\Post(path: '/api/v1/add-followers')]
     #[RequestParam(name: 'userId', requirements: '\d+')]
     #[RequestParam(name: 'followersLogin')]
     #[RequestParam(name: 'count', requirements: '\d+')]
     #[RequestParam(name: 'async', requirements: '0|1')]
     public function addFollowersAction(int $userId, string $followersLogin, int $count, int $async): Response
     {
         $user = $this->userManager->findUserById($userId);
         if ($user !== null) {
             if ($async === 0) {
                 $createdFollowers = $this->subscriptionService->addFollowers($user, $followersLogin, $count);
                 $view = $this->view(['created' => $createdFollowers], 200);
             } else {
                 // send multi messages
                 $messages = $this->subscriptionService->getFollowersMessages($user, $followersLogin, $count);
                 $result  = $this->asyncService->publishMultipleToExchange(AsyncService::ADD_FOLLOWER, $messages);
                 $view = $this->view(['success' => $result], $result ? 200 : 500);
             }
         } else {
             $view = $this->view(['success' => false], 404);
         }

         return $this->handleView($view);
     }






    #[Rest\Post(path: '/api/v1/add-followers-simple')]
    #[RequestParam(name: 'userId', requirements: '\d+')]
    #[RequestParam(name: 'followersLogin')]
    #[RequestParam(name: 'count', requirements: '\d+')]
    public function addFollowersActionSimple(int $userId, string $followersLogin, int $count): Response
    {
        $user = $this->userManager->findUserById($userId);
        if ($user !== null) {
            $createdFollowers = $this->subscriptionService->addFollowers($user, $followersLogin, $count);
            $view = $this->view(['created' => $createdFollowers], 200);
        } else {
            $view = $this->view(['success' => false], 404);
        }

        return $this->handleView($view);
    }





    #[Rest\Post(path: '/api/v1/add-followers-async-push-one-message')]
    #[RequestParam(name: 'userId', requirements: '\d+')]
    #[RequestParam(name: 'followersLogin')]
    #[RequestParam(name: 'count', requirements: '\d+')]
    #[RequestParam(name: 'async', requirements: '0|1')]
    public function addFollowersActionAsync(int $userId, string $followersLogin, int $count, int $async): Response
    {
        $user = $this->userManager->findUserById($userId);
        if ($user !== null) {
            if ($async === 0) {
                $createdFollowers = $this->subscriptionService->addFollowers($user, $followersLogin, $count);
                $view = $this->view(['created' => $createdFollowers], 200);
            } else {
                // Send one message
                $message = (new AddFollowersDTO($userId, $followersLogin, $count))->toAMQPMessage();
                $result  = $this->asyncService->publishToExchange(AsyncService::ADD_FOLLOWER, $message);
                $view = $this->view(['success' => $result], $result ? 200 : 500);
            }
        } else {
            $view = $this->view(['success' => false], 404);
        }

        return $this->handleView($view);
    }
}