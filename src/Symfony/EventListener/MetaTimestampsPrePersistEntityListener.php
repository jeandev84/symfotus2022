<?php
namespace App\Symfony\EventListener;

use App\Entity\Contract\HasMetaTimestampsInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class MetaTimestampsPrePersistEntityListener
{

    public function prePersist(LifecycleEventArgs $event)
    {
           $entity = $event->getObject();

           if ($entity instanceof HasMetaTimestampsInterface) {
                $entity->setCreatedAt();
                $entity->setUpdatedAt();
           }
     }
}