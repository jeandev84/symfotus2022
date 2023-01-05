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
           # e.g renders templates:
           # 'users/list.twig'
           # 'users/user-content.twig'
           # 'users/user-table.twig'
           /*
            return $this->render('users/user-vue.twig', [
              'users' => $this->userManager->getUserList()
            ]);
           */

           # 'users/user-vue.twig'
           return $this->render('users/user-vue.twig', [
              'users' => json_encode($this->userManager->getUsersListVue())
           ]);
      }
}