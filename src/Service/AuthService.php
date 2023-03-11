<?php
namespace App\Service;

use App\Manager\UserManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{

     private UserManager $userManager;


     private UserPasswordHasherInterface $passwordHasher;


     public function __construct(UserManager $userManager, UserPasswordHasherInterface $passwordHasher)
     {
         $this->userManager = $userManager;
         $this->passwordHasher = $passwordHasher;
     }




     /**
      * @param string $login
      * @param string $password
      * @return bool
     */
     public function isCredentialsValid(string $login, string $password): bool
     {
          $user = $this->userManager->findUserByLogin($login);

          if ($user === null) {
              return false;
          }

          return $this->passwordHasher->isPasswordValid($user, $password);
     }




     /**
      * @param string $login
      * @param string $password
      * @return bool
     */
     public function attempt(string $login, string $password): bool
     {
         return $this->isCredentialsValid($login, $password);
     }





     /**
      * @param string $login
      * @return string
     */
     public function getToken(string $login): string
     {
         return $this->userManager->updateUserToken($login);
     }
}