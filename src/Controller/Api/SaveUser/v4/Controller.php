<?php
namespace App\Controller\Api\SaveUser\v4;

use App\DTO\SaveUserDTO;
use App\Entity\User;
use App\Manager\UserManager;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
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

        // Если $_GET params
        // #[QueryParam(name: 'page')]

        // Если $_POST params
        // #[RequestParam(name: 'login')]
    }




    #[Rest\Post(path: '/api/v4/save-user')]
    #[RequestParam(name: 'login')]
    #[RequestParam(name: 'password')]
    #[RequestParam(name: 'roles')]
    #[RequestParam(name: 'age', requirements: '\d+')]
    #[RequestParam(name: 'isActive', requirements: 'true|false')]
    public function saveUserAction(string $login, string $password, string $roles, string $age, string $isActive): Response
    {
         $userDTO = new SaveUserDTO([
             'login'     => $login,
             'password'  => $password,
             'roles'     => json_decode($roles, true, 512, JSON_THROW_ON_ERROR),
             'age'       => (int)$age,
             'isActive'  => $isActive === 'true'
         ]);

         $userId = $this->userManager->saveUserFromDTO(new User(), $userDTO);
         [$data, $code] = ($userId === null) ? [['success' => false], 400] : [['id' => $userId], 200];
         return $this->handleView($this->view($data, $code));
    }
}