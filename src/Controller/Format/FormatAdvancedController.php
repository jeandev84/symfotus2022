<?php
namespace App\Controller\Format;

use App\Service\FormatService;
use App\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormatAdvancedController extends AbstractController
{

    private FormatService $formatService;
    private MessageService $messageService;


    public function __construct(FormatService $formatService, MessageService $messageService)
    {
         $this->formatService  = $formatService;
         $this->messageService = $messageService;
    }



    #[Route('/format-advanced', name: 'format.advanced')]
    public function format()
    {
        $result = $this->formatService->format($this->messageService->printMessages('world'));

        return new Response("<html><body>$result</body></html>");
    }
}