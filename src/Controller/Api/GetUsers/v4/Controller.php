<?php
namespace App\Controller\Api\GetUsers\v4;

use App\Manager\UserManager;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class Controller extends AbstractFOSRestController
{

    protected UserManager $userManager;


    /**
     * @param UserManager $userManager
    */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }



//    #[Rest\Get(path: '/api/v4/get-users')]
//    public function __invoke(Request $request)
//    {
//        $perPage = $request->request->get('perPage');
//        $page    = $request->request->get('page');
//        $users   = $this->userManager->getUsers($page ?? 0, $perPage ?? 20);
//        $code    = empty($users) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
//
//        return $this->handleView($this->view(['users' => $users], $code));
//    }
//


    #[Rest\Get(path: '/api/v4/get-users')]
    public function getUsersAction(Request $request): Response
    {
        $perPage = $request->request->get('perPage');
        $page    = $request->request->get('page');
        $users   = $this->userManager->getUsers($page ?? 0, $perPage ?? 20);
        $code    = empty($users) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
        $context = (new Context())->setGroups(['user:read', 'user:write']);
        $view = $this->view(['users' => $users], $code)->setContext($context);

        return $this->handleView($view);
    }
}