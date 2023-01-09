<?php
namespace App\EventSubscriber;

use App\Event\AppEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AppEventSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
         return [
            AppEvent::class => [
                ['onAppEvent', 1], // 1 priority
                ['onAppEventHigh', 1000], // 1000 is priority
            ]
         ];
    }


    public function onAppEvent(AppEvent $event)
    {
          dd($event->getMessage());
    }




    public function onAppEventHigh(AppEvent $event)
    {
        dd(strtoupper($event->getMessage()));
    }
}