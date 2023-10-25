<?php
namespace App\Manager;

use App\Entity\Tweet;
use App\Entity\User;
use App\Repository\TweetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TweetManager
{

    private const CACHE_TAG = 'tweets';


    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected TagAwareAdapterInterface $cache
    )
    {
    }

    public function postTweet(User $author, string $text): void
    {
        $tweet = new Tweet();
        $tweet->setAuthor($author);
        $tweet->setText($text);
        $tweet->setCreatedAt();
        $tweet->setUpdatedAt();
        $author->addTweet($tweet);
        $this->entityManager->persist($tweet);
        $this->entityManager->flush();
    }


    /**
     * @param int $page
     * @param int $perPage
     * @return Tweet[]
     * @throws InvalidArgumentException
     * @throws CacheException
    */
    public function getTweets(int $page, int $perPage): array
    {

        /** @var TweetRepository $tweetRepository */
        $tweetRepository = $this->entityManager->getRepository(Tweet::class);

        return $this->cache->get("tweets_{$page}_{$perPage}", function (ItemInterface $item) use ($tweetRepository, $page, $perPage){
            $tweets = $tweetRepository->getTweets($page, $perPage);
            $tweetSerialized = array_map(static fn(Tweet $tweet) => $tweet->toArray(), $tweets);
            $item->set($tweetSerialized);
            $item->tag(self::CACHE_TAG);

            return $tweetSerialized;
        });
    }


    /**
     * @throws InvalidArgumentException
    */
    public function saveTweet(int $authorId, string $text): bool
    {
          $tweet = new Tweet();
          $userRepository = $this->entityManager->getRepository(User::class);
          $author = $userRepository->find($authorId);
          if (!($author instanceof User)) {
              return false;
          }
          $tweet->setAuthor($author);
          $tweet->setText($text);
          #$tweet->setCreatedAt();
          #$tweet->setUpdatedAt();
          $this->entityManager->persist($tweet);
          $this->entityManager->flush();
          $this->cache->invalidateTags([self::CACHE_TAG]);
          return true;
    }
}