<?php
namespace App\Controller\Api\v1;

use App\Event\AppEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
       $this->eventDispatcher = $eventDispatcher;
    }



    #[Route(path: '/api/v1/data', methods: ['POST'])]
    public function postDataWithTestMessage(Request $request, string $message)
    {
        $this->eventDispatcher->dispatch(new AppEvent('My Message'));

        return new Response("<html><body></body></html>");
    }
}