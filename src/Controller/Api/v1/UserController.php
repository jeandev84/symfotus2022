<?php
namespace App\Controller\Api\v1;

use App\Entity\User;
use App\Event\CreateUserEvent;
use App\Exception\DeprecatedApiException;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route(path: '/api/v1/user')]
class UserController extends AbstractController
{

     private UserManager $userManager;

     private EventDispatcherInterface $eventDispatcher;


     public function __construct(
         UserManager $userManager,
         EventDispatcherInterface $eventDispatcher
     )
     {
          $this->userManager = $userManager;
          $this->eventDispatcher = $eventDispatcher;
     }



     #[Route(path: '', methods: ['POST'])]
     public function saveUserAction(Request $request): JsonResponse
     {
         throw new DeprecatedApiException('This API method is deprecated.');

         $login  = $request->request->get('login');
         $userId = $this->userManager->saveUser($login);
         [$data, $code] = $userId  === null ? [['success' => false], 400] : [['success' => true, 'userId' => $userId], 200];

         return new JsonResponse($data, $code);
     }




     #[Route(path: '', methods: ['GET'])]
     public function getUsersAction(Request $request): JsonResponse
     {
         $perPage = $request->query->get('perPage');
         $page    = $request->query->get('page');
         $users   = $this->userManager->getUsers($page ?? 0, $perPage ?? 20);
         $code    = empty($users) ? 204 : 200;

         return new JsonResponse([
             'users' => array_map(static fn(User $user) => $user->toArray(), $users)
         ],
             $code
         );
     }





     #[Route(path: '', methods: ['PATCH'])]
     public function updateUserAction(Request $request): JsonResponse
     {
         $userId = $request->query->get('userId');
         $login  = $request->query->get('login');

         $result = $this->userManager->updateUser($userId, $login);

         return new JsonResponse(['success' => $result !== null], (! $request !== null) ? 200 : 404);
     }




    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteUserByIdAction(int $id): JsonResponse
    {
        $result = $this->userManager->deleteUserById($id);

        return new JsonResponse(['success' => $result], $result ? 200 : 404);
    }





     #[Route(path: '/async', methods: ['POST'])]
     public function saveUserAsyncAction(Request $request)
     {
          /*  $this->eventDispatcher->dispatch(new CreateUserEvent($request->request->get('login'))); */

          $login = $request->request->get('login') . '_updated';
          $this->eventDispatcher->dispatch(new CreateUserEvent($login));

          return new JsonResponse(['success' => true], Response::HTTP_ACCEPTED);
     }
}