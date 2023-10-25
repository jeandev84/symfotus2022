<?php
namespace App\Controller\Format;

use App\Service\FormatService;
use App\Service\GreeterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class FormatController extends AbstractController
{

    private GreeterService $greeterService;
    private FormatService $formatService;


    public function __construct(GreeterService $greeterService, FormatService $formatService)
    {
        $this->greeterService = $greeterService;
        $this->formatService  = $formatService;
    }



    #[Route('/format-content', name: 'format.content')]
    public function format()
    {
        $result = $this->formatService->format($this->greeterService->greet('world'));

        return new Response("<html><body>$result</body></html>");
    }
}