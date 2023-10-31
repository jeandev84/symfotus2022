<?php
namespace App\Symfony\Decorator;

use StatsdBundle\Client\StatsdAPIClient;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Cache\ResettableInterface;
use Symfony\Contracts\Cache\CacheInterface;

class CountingAdapterDecorator implements AdapterInterface, CacheInterface, LoggerAwareInterface, ResettableInterface
{

    private const STATSD_HIT_PREFIX  = 'cache.hit.';
    private const STATSD_MISS_PREFIX = 'cache.miss.';

    private AbstractAdapter $adapter;
    private StatsdAPIClient $statsdAPIClient;


    /**
     * @param AbstractAdapter $adapter
     * @param StatsdAPIClient $statsdAPIClient
    */
    public function __construct(AbstractAdapter $adapter, StatsdAPIClient $statsdAPIClient)
    {
        $this->adapter = $adapter;
        $this->statsdAPIClient = $statsdAPIClient;
        $this->adapter->setCallbackWrapper(null);
    }


    /**
     * @param $key
     * @return CacheItem
     * @throws InvalidArgumentException
    */
    public function getItem($key): CacheItem
    {
        $result = $this->adapter->getItem($key);
        $this->incCounter($result);

        return $result;
    }


    /**
     * @inheritDoc
    */
    public function getItems(array $keys = [])
    {
        $result = $this->adapter->getItems($keys);

        foreach ($result as $item) {
            $this->incCounter($item);
        }

        return $result;
    }



    /**
     * @param string $prefix
     * @return bool
    */
    public function clear(string $prefix = ''): bool
    {
         return $this->adapter->clear($prefix);
    }


    /**
     * @param string $key
     * @param callable $callback
     * @param float|null $beta
     * @param array|null $metadata
     * @return mixed
     * @throws InvalidArgumentException
    */
    public function get(string $key, callable $callback, float $beta = null, array &$metadata = null)
    {
         return $this->adapter->get($key, $callback, $beta, $metadata);
    }




    /**
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
    */
    public function delete(string $key): bool
    {
         return $this->adapter->delete($key);
    }




    /**
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
    */
    public function hasItem(string $key): bool
    {
        return $this->adapter->hasItem($key);
    }




    /**
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
    */
    public function deleteItem(string $key)
    {
        return $this->adapter->deleteItem($key);
    }



    /**
     * @param array $keys
     * @return bool
     * @throws InvalidArgumentException
    */
    public function deleteItems(array $keys): bool
    {
        return $this->adapter->deleteItems($keys);
    }



    /**
     * @inheritDoc
    */
    public function save(CacheItemInterface $item): bool
    {
         return $this->adapter->save($item);
    }


    /**
     * @inheritDoc
    */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        return $this->adapter->saveDeferred($item);
    }



    /**
     * @inheritDoc
    */
    public function commit(): bool
    {
        return $this->adapter->commit();
    }



    /**
     * @inheritDoc
    */
    public function setLogger(LoggerInterface $logger)
    {
         $this->adapter->setLogger($logger);
    }




    public function reset(): void
    {
        $this->adapter->reset();
    }




    /**
     * Счётчик
     *
     * @param CacheItemInterface $cacheItem
     * @return void
    */
    private function incCounter(CacheItemInterface $cacheItem): void
    {
         if ($cacheItem->isHit()) {
             $this->statsdAPIClient->increment(self::STATSD_HIT_PREFIX.$cacheItem->getKey());
         } else {
             $this->statsdAPIClient->increment(self::STATSD_MISS_PREFIX.$cacheItem->getKey());
         }
    }
}