<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DemoController extends AbstractController
{

      #[Route('/demo-page', name: 'demo.page')]
      public function world(): Response
      {
           return new Response('<html><body><h1><b>Hello,</b> <i>world</i></h1></body></html>');
      }
}