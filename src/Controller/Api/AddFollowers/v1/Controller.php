<?php
namespace App\Controller\Api\GetTweets\v1;


use App\Manager\UserManager;
use App\Service\SubscriptionService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/add-followers')]
class Controller extends AbstractFOSRestController
{

     private SubscriptionService $subscriptionService;
     private UserManager $userManager;

     public function __construct(SubscriptionService $subscriptionService, UserManager $userManager)
     {
         $this->subscriptionService = $subscriptionService;
         $this->userManager = $userManager;
     }



     #[Route(path: '', methods: ['POST'])]
     #[RequestParam(name: 'userId', requirements: '\d+')]
     #[RequestParam(name: 'followersLogin')]
     #[RequestParam(name: 'count', requirements: '\d+')]
     public function addFollowersAction(int $userId, string $followersLogin, int $count): Response
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
}