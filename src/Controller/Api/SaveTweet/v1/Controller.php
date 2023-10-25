<?php
namespace App\Controller\Api\SaveTweet\v1;

use App\Entity\Tweet;
use App\Manager\TweetManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/tweet')]
class Controller extends AbstractFOSRestController
{

    public function __construct(private TweetManager $tweetManager)
    {
    }



    #[Route(path: '', methods: ['POST'])]
    #[RequestParam(name: 'authorId', requirements: '\d+')]
    #[RequestParam(name: 'text')]
    public function saveTweetsAction(int $authorId, string $text): Response
    {
        $tweetId = $this->tweetManager->saveTweet($authorId, $text);
        [$data, $code] = ($tweetId === null ? [['success' => false], 400] : [['tweet' => $tweetId], 200]);

        return $this->handleView($this->view($data, $code));
    }
}