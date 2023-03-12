<?php
namespace App\Service;

use App\Manager\UserManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{

     public function __construct(
         protected UserManager $userManager,
         protected UserPasswordHasherInterface $passwordHasher,
         protected JWTEncoderInterface $jwtEncoder,
         protected int $tokenTTL
     )
     {

     }




     /**
      * @param string $login
      * @param string $password
      * @return bool
     */
     public function attempt(string $login, string $password): bool
     {
          $user = $this->userManager->findUserByLogin($login);

          if ($user === null) {
              return false;
          }

          return $this->passwordHasher->isPasswordValid($user, $password);
     }





     /**
      * @param string $login
      * @return string|null
      * @throws JWTEncodeFailureException
     */
     public function getToken(string $login): ?string
     {
         // Token data
         $tokenData = ['username' => $login, 'exp' => time() + $this->tokenTTL];

         return $this->jwtEncoder->encode($tokenData);
     }
}