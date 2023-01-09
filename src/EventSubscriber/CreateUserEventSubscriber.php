<?php
namespace App\EventSubscriber;

use App\Event\CreateUserEvent;
use App\Manager\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateUserEventSubscriber implements EventSubscriberInterface
{

    private UserManager $userManager;


    /**
     * @param UserManager $userManager
    */
    public function __construct(UserManager $userManager)
    {
         $this->userManager = $userManager;
    }


    public static function getSubscribedEvents()
    {
         return [
            CreateUserEvent::class => 'onCreateUser'
         ];
    }




    /**
     * @param CreateUserEvent $event
     * @return void
    */
    public function onCreateUser(CreateUserEvent $event)
    {
         $this->userManager->saveUser($event->getLogin());
    }
}