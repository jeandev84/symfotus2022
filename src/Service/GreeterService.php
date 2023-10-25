<?php
namespace App\Service;

class GreeterService
{

     private string $greet;


     public function __construct(string $greet)
     {
          $this->greet = $greet;
     }


     /**
      * @param string $name
      * @return string
     */
     public function greet(string $name): string
     {
         return $this->greet. ', '. $name. '!';
     }
}