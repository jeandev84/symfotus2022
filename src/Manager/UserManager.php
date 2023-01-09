<?php
namespace App\Manager;

use App\DTO\SaveUserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class UserManager
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private FormFactoryInterface $formFactory
    )
    {

    }



    public function getSaveForm(): FormInterface
    {
         return $this->formFactory->createBuilder()
                     ->add('login', TextType::class)
                     ->add('password', PasswordType::class)
                     ->add('age', IntegerType::class)
                     ->add('isActive', CheckboxType::class, ['required' => false])
                     ->add('submit', SubmitType::class)
                     ->getForm();
    }





    /**
     * @param User $user
     * @param SaveUserDTO $saveUserDTO
     * @return int|null
    */
    public function saveUserFromDTO(User $user, SaveUserDTO $saveUserDTO): ?int
    {
         $user->setLogin($saveUserDTO->login);
         $user->setPassword($saveUserDTO->password);
         $user->setAge($saveUserDTO->age);
         $user->setIsActive($saveUserDTO->isActive);

         $this->entityManager->persist($user);
         $this->entityManager->flush();

         return $user->getId();
    }



    /**
     * @param string $login
     * @return int|null
    */
    public function saveUser(string $login): ?int
    {
        $user = new User();
        $user->setLogin($login);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getId();
    }


    /**
     * @param int $userId
     * @param string $login
     * @return User|null
    */
    public function updateUser(int $userId, string $login): ?User
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        /** @var User $user */
        $user = $userRepository->find($userId);

        if ($user === null) {
            return null;
        }

        $user->setLogin($login);
        $this->entityManager->flush();

        return $user;
    }




    /**
     * @param int $userId
     * @return bool
    */
    public function deleteUserById(int $userId): bool
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        /** @var User $user */
        $user = $userRepository->find($userId);

        if ($user === null) {
            return false;
        }


        return $this->deleteUser($user);
    }




    /**
     * @param User $user
     * @return bool
    */
    public function deleteUser(User $user): bool
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return true;
    }



    /**
     * @return User[]
     */
    public function getUsers(int $page, int $perPage): array
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        return $userRepository->getUsers($page, $perPage);
    }


    /**
     * @param int $userId
     * @return FormInterface|null
    */
    public function getUpdateForm(int $userId): ?FormInterface
    {

         /** @var UserRepository $userRepository */
         $userRepository = $this->entityManager->getRepository(User::class);

         $user = $userRepository->find($userId);

         if ($user === null) {
             return null;
         }

         return $this->formFactory->createBuilder(FormType::class, SaveUserDTO::fromEntity($user))
                                  ->add('login', TextType::class)
                                  ->add('password', PasswordType::class)
                                  ->add('age', IntegerType::class)
                                  ->add('isActive', CheckboxType::class, ['required' => false])
                                  ->add('submit', SubmitType::class)
                                  ->setMethod('PATCH')
                                  ->getForm();
    }





    /**
     * @param int $userId
     * @param SaveUserDTO $saveUserDTO
     * @return false|int|null
    */
    public function updateUserFromDTO(int $userId, SaveUserDTO $saveUserDTO)
    {
         /** @var UserRepository $userRepository */
         $userRepository = $this->entityManager->getRepository(User::class);

         /** @var User */
         $user = $userRepository->find($userId);

         if ($user === null) {
             return false;
         }

         return $this->saveUserFromDTO($user, $saveUserDTO);
    }

}