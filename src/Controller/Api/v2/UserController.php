<?php
namespace App\Controller\Api\v2;

use App\Entity\User;
use App\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/api/v2/user')]
class UserController extends AbstractController
{

    private UserManager $userManager;


    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }



    #[Route(path: '')]
    #[Route(methods: ['POST'])]
    public function saveUserAction(Request $request): JsonResponse
    {
        $login  = $request->request->get('login');
        $userId = $this->userManager->saveUser($login);
        [$data, $code] = $userId  === null ?
            [['success' => false], 400] :
            [['success' => true, 'userId' => $userId], 200];

        return new JsonResponse($data, $code);
    }




    #[Route(path: '')]
    #[Route(methods: ['GET'])]
    public function getUsersAction(Request $request): JsonResponse
    {
        $perPage = $request->query->get('perPage');
        $page    = $request->query->get('page');
        $users = $this->userManager->getUsers($page ?? 0, $perPage ?? 20);
        $code = empty($users) ? 204 : 200;

        return new JsonResponse(['users' => array_map(static fn(User $user) => $user->toArray(), $users)], $code);
    }



    #[Route(path: '/by-login/{user_login}', methods: ['GET'], priority: 2)]
    #[Method(['GET'])]
    #[ParamConverter('user', options: ['mapping' => ['user_login' => 'login']])]
    public function getUserByLoginAction(User $user)
    {
        return new JsonResponse(['user' => $user->toArray()], 200);
    }



    #[Route(path: '/{user_id}', requirements: ['user_id' => '\d+'])]
    #[Method(['DELETE'])]
    #[Entity('user', expr: 'repository.find(user_id)')]
    public function deleteUserAction(User $user): JsonResponse
    {
        $result = $this->userManager->deleteUser($user);

        return new JsonResponse(['success' => $result], $result ? 200 : 404);
    }




    #[Route(path: '')]
    #[Method(['PATCH'])]
    public function updateUserAction(Request $request): JsonResponse
    {
        $userId = $request->query->get('userId');
        $login  = $request->query->get('login');

        $result = $this->userManager->updateUser($userId, $login);
        [$data, $code] = (! $result) ? [null, 404] : [['user' => $result->toArray()], 200];

        return new JsonResponse($data, $code);
    }
}