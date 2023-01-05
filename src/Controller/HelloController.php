<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class HelloController
{

      public function world(): Response
      {
           return new Response('<html><body><h1><b>Hello,</b> <i>world</i></h1></body></html>');
      }
}