<?php
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AppEvent extends Event
{

     private string $message;


     public function __construct(string $message)
     {
         $this->message = $message;
     }


     /**
      * @return string
     */
     public function getMessage(): string
     {
        return $this->message;
     }
}