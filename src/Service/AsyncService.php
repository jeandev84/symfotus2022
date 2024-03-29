<?php
namespace App\Service;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class AsyncService
{

    public const ADD_FOLLOWER        = 'add_follower';
    public const PUBLISH_TWEET       = 'publish_tweet';
    public const SEND_NOTIFICATION   = 'send_notification';
    public const UPDATE_FEED         = 'update_feed';



    /** @var ProducerInterface[] */
    private array $producers;

    public function __construct()
    {
        $this->producers = [];
    }

    public function registerProducer(string $producerName, ProducerInterface $producer): void
    {
        $this->producers[$producerName] = $producer;
    }


    /**
     * Push one message
     *
     * @param string $producerName
     * @param string $message
     * @param string|null $routingKey
     * @param array|null $additionalProperties
     * @return bool
    */
    public function publishToExchange(string $producerName, string $message, ?string $routingKey = null, ?array $additionalProperties = null): bool
    {
        if (isset($this->producers[$producerName])) {
            $this->producers[$producerName]->publish($message, $routingKey ?? '', $additionalProperties ?? []);
            return true;
        }

        return false;
    }




    /**
     * Push multiple messages
     *
     * @param string $producerName
     * @param array $messages
     * @param string|null $routingKey
     * @param array|null $additionalProperties
     * @return int
    */
    public function publishMultipleToExchange(string $producerName, array $messages, ?string $routingKey = null, ?array $additionalProperties = null): int
    {
        $sentCount = 0;
        if (isset($this->producers[$producerName])) {
            foreach ($messages as $message) {
                $this->producers[$producerName]->publish($message, $routingKey ?? '', $additionalProperties ?? []);
                $sentCount++;
            }

            return $sentCount;
        }

        return $sentCount;
    }
}