<?php
namespace App\Controller;

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
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class FormController extends AbstractController
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
     * @param UserManager $userManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param Environment $twig
     */
    public function __construct(
        UserManager $userManager,
        EventDispatcherInterface $eventDispatcher,
        Environment $twig
    )
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->twig = $twig;
    }




    #[Route(path: '/form', methods: ['GET'])]
    public function getSaveFormAction(): Response
    {
        $form = $this->userManager->getSaveForm();
        $content = $this->twig->render('form/form.twig', [
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

        $content = $this->twig->render('form/form.twig', [
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
    #[Route(path: '/app/form/{id}', methods: ['GET'], requirements: ['id' => "\d+"])]
    public function getUpdateFormAction(int $id): Response
    {
        $form = $this->userManager->getUpdateForm($id);

        if ($form === null) {
            return new JsonResponse(['message' => "User with ID $id not found"], 404);
        }

        $content = $this->twig->render('form/form.twig', [
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
    #[Route(path: '/app/form/{id}', methods: ['PATCH'], requirements: ['id' => "\d+"])]
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

        $content = $this->twig->render('form/form.twig', [
            'form' => $form->createView()
        ]);

        return new Response($content);
    }
}