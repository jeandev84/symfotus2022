<?php

namespace App\EventListener;

use App\Exception\DeprecatedApiException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class MessageArgumentListener
{

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $event->setArguments(['message' => 'My message']);
    }

}