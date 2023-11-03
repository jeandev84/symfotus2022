<?php
namespace App\Entity\Contract;

interface HasAsyncMessagePersisterInterface
{
     /**
      * Returns producer name
      *
      * @return string
     */
     public function getProducerName(): string;



     /**
      * Returns the message for AMPQ
      *
      * @return string
     */
     public function toAMPQMessage(): string;
}