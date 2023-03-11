<?php
namespace App\Controller\Api\SaveUser\v5;

use App\Controller\Common\ErrorResponseTrait;
use App\Controller\Api\SaveUser\v5\Input\SaveUserDTO;
use App\Entity\User;
use App\Manager\UserManager;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;


class Controller extends AbstractFOSRestController
{

    use ErrorResponseTrait;


    protected SaveUserManager $saveUserManager;



    /**
     * @param SaveUserManager $userManager
    */
    public function __construct(SaveUserManager $saveUserManager)
    {
        $this->saveUserManager = $saveUserManager;
    }




    #[Rest\Post(path: '/api/v5/save-user')]
    public function saveUserAction(SaveUserDTO $request, ConstraintViolationListInterface $validationErrors): Response
    {
         if ($validationErrors->count()) {
             $view = $this->createValidationErrorResponse(Response::HTTP_BAD_REQUEST, $validationErrors);
             return $this->handleView($view);
         }

         $user = $this->saveUserManager->saveUser($request);
         [$data, $code] = ($user->id === null) ? [['success' => false], 400] : [['user' => $user], 200];
         return $this->handleView($this->view($data, $code));
    }
}