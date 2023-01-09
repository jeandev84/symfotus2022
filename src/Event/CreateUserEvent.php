<?php
namespace App\Event;

class CreateUserEvent
{
     private string $login;

     public function __construct(string $login)
     {
          $this->login = $login;
     }


     public function getLogin()
     {
         return $this->login;
     }
}