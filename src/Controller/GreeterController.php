<?php
namespace App\Controller;

use App\Service\GreeterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class GreeterController extends AbstractController
{

     private GreeterService $greeterService;

     public function __construct(GreeterService $greeterService)
     {
          $this->greeterService = $greeterService;
     }



     #[Route('/hello-world', name: 'hello.world')]
     public function hello()
     {
          return new Response(
  "<html>
             <body>
                {$this->greeterService->greet('world')}
             </body>
          </html>"
          );
     }
}