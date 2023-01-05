<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{

      #[Route('/hello/world', name: 'hello_world')]
      public function world(): Response
      {
           return new Response('<html><body><h1><b>Hello,</b> <i>world</i></h1></body></html>');
      }
}