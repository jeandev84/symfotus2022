<?php
namespace App\Controller\Api\GetTweets\v1;

use App\Entity\Tweet;
use App\Manager\TweetManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/tweet')]
class Controller extends AbstractFOSRestController
{

     public function __construct(private TweetManager $tweetManager)
     {
     }



     #[Route(path: '', methods: ['GET'])]
     public function getTweetsAction(Request $request): Response
     {
         $perPage = $request->query->getInt('perPage');
         $page    = $request->query->getInt('page', 20);
         $tweets  = $this->tweetManager->getTweets($page, $perPage);
         $code    = empty($tweets) ? 204 : 200;
         $view    = $this->view(['tweets' => array_map(static fn(Tweet $tweet) => $tweet->toArray(), $tweets)], $code);

         return $this->handleView($view);
     }
}