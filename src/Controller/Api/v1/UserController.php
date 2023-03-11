<?php
namespace App\Controller\Api\v1;

use App\DTO\SaveUserDTO;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


#[Route(path: '/api/v1/user')]
class UserController extends AbstractController
{

     /**
      * @var UserManager
     */
     private UserManager $userManager;


     /**
      * @var EventDispatcherInterface
     */
     private EventDispatcherInterface $eventDispatcher;



     /**
      * @var Environment
     */
     private Environment $twig;




     /**
      * @var ValidatorInterface
     */
     private ValidatorInterface $validator;




     /**
      * @param UserManager $userManager
      * @param EventDispatcherInterface $eventDispatcher
      * @param Environment $twig
      * @param ValidatorInterface $validator
     */
     public function __construct(
         UserManager $userManager,
         EventDispatcherInterface $eventDispatcher,
         Environment $twig,
         ValidatorInterface $validator
     )
     {
          $this->userManager = $userManager;
          $this->eventDispatcher = $eventDispatcher;
          $this->twig = $twig;
          $this->validator = $validator;
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




     #[Route(path: '/form', methods: ['GET'])]
     public function getSaveFormAction(): Response
     {
          $form = $this->userManager->getSaveForm();
          $content = $this->twig->render('users/form/form.twig', [
              'form' => $form->createView()
          ]);

          return new Response($content);
     }




     #[Route(path: '/form', methods: ['POST'])]
     public function saveUserFormAction(Request $request)
     {
         $form = $this->userManager->getSaveForm();

         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {

             $userDto = new SaveUserDTO($form->getData());
             $errors = $this->validator->validate($userDto);

             if (count($errors) > 0) {
                 return new JsonResponse(['errors' => $errors], 400);
             }

             $userId = $this->userManager->saveUserFromDTO(new User(), $userDto);
             [$data, $code] = ($userId === null) ? [['success' => false], 400] : [['id' => $userId], 200];

             return new JsonResponse($data, $code);
         }

         $content = $this->twig->render('users/form/form.twig', [
             'form' => $form->createView()
         ]);

         return new Response($content);
     }


     /**
      * @param int $id
      * @return Response
      * @throws LoaderError
      * @throws RuntimeError
      * @throws SyntaxError
     */
     #[Route(path: '/form/{id}', methods: ['GET'], requirements: ['id' => "\d+"])]
     public function getUpdateFormAction(int $id): Response
     {
          $form = $this->userManager->getUpdateForm($id);

          if ($form === null) {
              return new JsonResponse(['message' => "User with ID $id not found"], 404);
          }

          $content = $this->twig->render('users/form/form.twig', [
              'form' => $form->createView()
          ]);

          return new Response($content);
     }




     /**
      * @param Request $request
      * @param int $id
      * @return Response
      * @throws LoaderError
      * @throws RuntimeError
      * @throws SyntaxError
     */
     #[Route(path: '/form/{id}', methods: ['PATCH'], requirements: ['id' => "\d+"])]
     public function updateUserFormAction(Request $request, int $id): Response
     {
         $form = $this->userManager->getUpdateForm($id);

         if ($form === null) {
             return new JsonResponse(['message' => "User with ID $id not found"], 404);
         }

         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
             $result = $this->userManager->updateUserFromDTO($id, $form->getData());
             return new JsonResponse(['success' => $result], $result ? 200 : 400);
         }

         $content = $this->twig->render('users/form/form.twig', [
             'form' => $form->createView()
         ]);

         return new Response($content);
     }
}