<?php
namespace App\Controller\Api\v2;

use App\DTO\SaveUserDTO;
use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Twig\Environment;


#[Route(path: '/api/v2/user')]
class UserController extends AbstractController
{

    private UserManager $userManager;

    private Environment $twig;


    public function __construct(UserManager $userManager, Environment $twig)
    {
         $this->userManager = $userManager;
         $this->twig        = $twig;
    }



    #[Route(path: '/form', methods: ['GET'])]
    public function getSaveFormAction(): Response
    {
         // api/v2/user/form
         $form = $this->userManager->getSaveForm();
         $content = $this->twig->render('users/form.twig', [
             'userForm' => $form->createView()
         ]);

         return new Response($content);
    }




    #[Route(path: '/form', methods: ['POST'])]
    public function saveUserFormAction(Request $request): Response
    {
         $form = $this->userManager->getSaveForm();

         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {

              $userId = $this->userManager->saveUserFromDTO(new User(), new SaveUserDTO($form->getData()));
              [$data, $code] = ($userId === null) ? [['success' => false], 400] : [['id' => $userId], 200];

              return new JsonResponse($data, $code);
         }

         $content = $this->twig->render('users/form.twig', [
             'userForm' => $form->createView()
         ]);

         return new Response($content);
    }



    #[Route(path: '/form/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function getUpdateFormAction(int $id): Response
    {
          $form = $this->userManager->getUpdateForm($id);

          if ($form === null) {
               return new JsonResponse(['message' => "User with ID {$id} not found"], 404);
          }

          $content = $this->twig->render('users/form.twig', [
              'form' => $form->createView()
          ]);

          return new Response($content);
    }




    #[Route(path: '/form/{id}', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function updateUserFormAction(Request $request, int $id)
    {
          $form = $this->userManager->getUpdateForm($id);

          if ($form === null) {
              return new JsonResponse(['message' => "User with ID {$id} not found"], 404);
          }

          $form->handleRequest($request);

          if ($form->isSubmitted() && $form->isValid()) {

              $result = $this->userManager->updateUserFromDTO($id, $form->getData());

              return new JsonResponse(['success' => $result], $result ? 200 : 400);
          }

          $content = $this->twig->render('users/form.twig', [
             'form' => $form->createView()
          ]);

          return new Response($content);
    }





    #[Route(path: '', methods: ['POST'])]
    public function saveUserAction(Request $request): JsonResponse
    {
        $login  = $request->request->get('login');
        $userId = $this->userManager->saveUser($login);
        [$data, $code] = $userId  === null ?
            [['success' => false], 400] :
            [['success' => true, 'userId' => $userId], 200];

        return new JsonResponse($data, $code);
    }




    #[Route(path: '', methods: ['GET'])]
    public function getUsersAction(Request $request): JsonResponse
    {
        $perPage = $request->query->get('perPage');
        $page    = $request->query->get('page');
        $users = $this->userManager->getUsers($page ?? 0, $perPage ?? 20);
        $code = empty($users) ? 204 : 200;

        return new JsonResponse(['users' => array_map(static fn(User $user) => $user->toArray(), $users)], $code);
    }



    #[Route(path: '/by-login/{user_login}', methods: ['GET'], priority: 2)]
    #[ParamConverter('user', options: ['mapping' => ['user_login' => 'login']])]
    public function getUserByLoginAction(User $user)
    {
        return new JsonResponse(['user' => $user->toArray()], 200);
    }



    #[Route(path: '/{user_id}', requirements: ['user_id' => '\d+'], methods: ['DELETE'])]
    #[Entity('user', expr: 'repository.find(user_id)')]
    public function deleteUserAction(User $user): JsonResponse
    {
        $result = $this->userManager->deleteUser($user);

        return new JsonResponse(['success' => $result], $result ? 200 : 404);
    }




    #[Route(path: '', methods: ['PATCH'])]
    public function updateUserAction(Request $request): JsonResponse
    {
        $userId = $request->query->get('userId');
        $login  = $request->query->get('login');

        $result = $this->userManager->updateUser($userId, $login);
        [$data, $code] = (! $result) ? [null, 404] : [['user' => $result->toArray()], 200];

        return new JsonResponse($data, $code);
    }



    #[Route(path: '/customHeader', methods: ['GET'])]
    public function getCustomHeader(Request $request)
    {
         $request->headers->get('My-header');
    }
}