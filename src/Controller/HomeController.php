<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

     #[Route(path: '/', methods: ['GET'])]
     public function index(): Response
     {
          return new Response("Home page [". __METHOD__ ."]");
     }
}