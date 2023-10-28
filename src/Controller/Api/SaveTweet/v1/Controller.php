<?php
namespace App\Controller\Api\SaveTweet\v1;

use App\Controller\Common\ErrorResponseTrait;
use App\Entity\Tweet;
use App\Manager\TweetManager;
use App\Service\FeedService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractFOSRestController
{

    use ErrorResponseTrait;

    private TweetManager $tweetManager;
    private FeedService  $feedService;


    /**
     * @param TweetManager $tweetManager
     *
     * @param FeedService $feedService
    */
    public function __construct(TweetManager $tweetManager, FeedService  $feedService)
    {
        $this->tweetManager = $tweetManager;
        $this->feedService  = $feedService;
    }





    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    #[Route(path: '/api/v1/tweet', methods: ['POST'])]
    #[RequestParam(name: 'authorId', requirements: '\d+')]
    #[RequestParam(name: 'text')]
    #[RequestParam(name: 'async', requirements: '0|1', nullable: true)]
    public function saveTweetAction(int $authorId, string $text, ?int $async): Response
    {
        $tweet = $this->tweetManager->saveTweet($authorId, $text);
        $success = $tweet !== null;
        if ($success) {
            if ($async === 1) {
                $this->feedService->spreadTweetAsync($tweet);
            } else {
                $this->feedService->spreadTweetSync($tweet);
            }
        }
        $code = $success ? 200 : 400;

        return $this->handleView($this->view(['success' => $success], $code));
    }
}