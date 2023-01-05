<?php
namespace App\Controller;

use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;



class UserController extends AbstractController
{

      private UserManager $userManager;


      public function __construct(UserManager $userManager)
      {
           $this->userManager = $userManager;
      }




      /**
       * @return Response
      */
      public function list(): Response
      {
           # 'users/list.twig'
           return $this->render('users/user-content.twig', [
              'users' => $this->userManager->getUserList()
           ]);
      }
}