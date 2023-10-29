<?php
namespace App\Manager;

use App\DTO\SaveUserDTO;
use App\Entity\User;
use App\Form\LinkedUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\Query\QueryString;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Paginator\FantaPaginatorAdapter;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{

    private EntityManagerInterface $entityManager;

    private FormFactoryInterface $formFactory;

    private UserPasswordHasherInterface $userPasswordHasher;

    private PaginatedFinderInterface $finder;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, UserPasswordHasherInterface $userPasswordHasher, PaginatedFinderInterface $finder)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->finder = $finder;
    }




    /**
     * @param int $id
     *
     * @return User|null
    */
    public function getUserById(int $id): ?User
    {
         $repository = $this->entityManager->getRepository(User::class);

         return $repository->find($id);
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
     * @throws \JsonException
     */
    public function saveUserFromDTO(User $user, SaveUserDTO $saveUserDTO): ?int
    {
         $hashedPassword = $this->userPasswordHasher->hashPassword($user, $saveUserDTO->password);

         $user->setLogin($saveUserDTO->login);
         $user->setPassword($hashedPassword);
         $user->setAge($saveUserDTO->age);
         $user->setIsActive($saveUserDTO->isActive);
         $user->setRoles($saveUserDTO->roles);
         $user->setPhone($saveUserDTO->phone);
         $user->setEmail($saveUserDTO->email);
         $user->setPreferred($saveUserDTO->preferred);

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
     * @throws \JsonException
    */
    public function getUpdateForm(int $userId): ?FormInterface
    {

         /** @var UserRepository $userRepository */
         $userRepository = $this->entityManager->getRepository(User::class);

         $user = $userRepository->find($userId);

         if ($user === null) {
             return null;
         }

         /*
         return $this->formFactory->createBuilder(FormType::class, SaveUserDTO::fromEntity($user))
                                  ->add('login', TextType::class)
                                  ->add('password', PasswordType::class)
                                  ->add('age', IntegerType::class)
                                  ->add('isActive', CheckboxType::class, ['required' => false])
                                  ->add('submit', SubmitType::class)
                                  ->setMethod('PATCH')
                                  ->getForm();
         */


        return $this->formFactory->createBuilder(FormType::class, SaveUserDTO::fromEntity($user))
                                ->add('login', TextType::class)
                                ->add('password', PasswordType::class, ['required' => false])
                                ->add('age', IntegerType::class)
                                ->add('isActive', CheckboxType::class, ['required' => false])
                                ->add('submit', SubmitType::class)
                                ->add('followers', CollectionType::class, [
                                     'entry_type'    => LinkedUserType::class,
                                     'entry_options' => ['label' => false],
                                     'allow_add'     => true
                                ])
                                ->setMethod('PATCH')
                                ->getForm();
    }





    /**
     * @param int $userId
     * @param SaveUserDTO $userDTO
     * @return mixed
    */
    public function updateUserFromDTO(int $userId, SaveUserDTO $userDTO)
    {
         /** @var UserRepository $userRepository */
         $userRepository = $this->entityManager->getRepository(User::class);

         /** @var User $user */
         $user = $userRepository->find($userId);

         if ($user === null) {
             return false;
         }


         // reset follwers
         $user->resetFollowers();


         // Add followers
         foreach ($userDTO->followers as $followerData) {

             $followerUserDTO = new SaveUserDTO($followerData);

             /** @var User $followerUser */
             if (isset($followerData['id'])) {

                 $followerUser = $userRepository->find($followerData['id']);

                 if ($followerUser !== null) {
                     $this->saveUserFromDTO($followerUser, $followerUserDTO);
                 }

             } else {

                 $followerUser = new User();
                 $this->saveUserFromDTO($followerUser, $followerUserDTO);
             }

             $user->addFollower($followerUser);
         }

         return $this->saveUserFromDTO($user, $userDTO);
    }




    public function findUserById(int $userId): ?User
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        return $userRepository->find($userId);
    }




    public function findUserByLogin(string $login): ?User
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        return $userRepository->findOneBy(['login' => $login]);
    }





    public function updateUserToken(string $login): ?string
    {
         $user = $this->findUserByLogin($login);

         if ($user === null) {
              return false;
         }

         $token = base64_encode(random_bytes(20));
         $user->setToken($token);

         $this->entityManager->flush();

         return $token;
    }




    public function findUserByToken(string $token): ?User
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        return $userRepository->findOneBy(['token' => $token]);
    }



    public function create(string $login): User
    {
        $user = new User();
        $user->setLogin($login);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }





    /**
     * ElasticSearch users by query
     *
     * @return User[]
    */
    public function findUserByQuery(string $query, int $perPage, int $page): array
    {
        $paginatedResult = $this->finder->findPaginated($query);
        $paginatedResult->setMaxPerPage($perPage);
        $paginatedResult->setCurrentPage($page);
        $result = [];
        array_push($result, ...$paginatedResult->getCurrentPageResults());

        return $result;
    }





    /**
     * ElasticSearch user with aggregation
     *
     * @return User[]
    */
    public function findUserWithAggregation(string $field): array
    {
        $aggregation = new Terms('notifications');
        $aggregation->setField($field);
        $query = new Query();
        $query->addAggregation($aggregation);
        $paginatedResult = $this->finder->findPaginated($query);
        /** @var FantaPaginatorAdapter $adapter */
        $adapter = $paginatedResult->getAdapter();

        return $adapter->getAggregations();
    }




    /**
     * ElasticSearch users by query with aggregation
     *
     * @return User[]
     */
    public function findUserByQueryWithAggregation(string $queryString, string $field): array
    {
        // notifications mean that in our case ( sms or email )
        $aggregation = new Terms('notifications');
        $aggregation->setField($field);
        $query = new Query(new QueryString($queryString));
        $query->addAggregation($aggregation);
        $paginatedResult = $this->finder->findPaginated($query);
        /** @var FantaPaginatorAdapter $adapter */
        $adapter = $paginatedResult->getAdapter();

        return $adapter->getAggregations();
    }


    /**
     * @param User $author
     * @param User $follower
     * @return void
    */
    public function subscribeUser(User $author, User $follower): void
    {
        $author->addFollower($follower);
        $follower->addAuthor($author);
        $this->entityManager->flush();
    }
}