<?php
namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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



//    public function clearEntityManager(): void
//    {
//        $this->entityManager->clear();
//    }
//
//    public function findUser(int $id): ?User
//    {
//        $repository = $this->entityManager->getRepository(User::class);
//        $user = $repository->find($id);
//
//        return $user instanceof User ? $user : null;
//    }
//
//    public function subscribeUser(User $author, User $follower): void
//    {
//        $author->addFollower($follower);
//        $follower->addAuthor($author);
//        $this->entityManager->flush();
//    }
//
//
//
//
//
//    /**
//     * @return User[]
//     */
//    public function findUsersByLogin(string $name): array
//    {
//        return $this->entityManager->getRepository(User::class)->findBy(['login' => $name]);
//    }
//
//    /**
//     * @return User[]
//     */
//    public function findUsersByCriteria(string $login): array
//    {
//        $criteria = Criteria::create();
//        /** @noinspection NullPointerExceptionInspection */
//        $criteria->andWhere(Criteria::expr()->eq('login', $login));
//        /** @var EntityRepository $repository */
//        $repository = $this->entityManager->getRepository(User::class);
//
//        return $repository->matching($criteria)->toArray();
//    }
//
//
//
//
//
//    /**
//     * @param int $userId
//     * @param string $login
//     * @return User|null
//    */
//    public function updateUserLogin(int $userId, string $login): ?User
//    {
//          $user = $this->findUser($userId);
//
//          if (! ($user instanceof User)) {
//               return null;
//          }
//
//          $user->setLogin($login);
//          $this->entityManager->flush();
//
//          return $user;
//    }
//
//
//
//
//
//    public function findUsersWithQueryBuilder(string $login): array
//    {
//          $queryBuilder = $this->entityManager->createQueryBuilder();
//
//          # DQL
//          $queryBuilder->select('u')
//                       ->from(User::class, 'u')
//                       ->andWhere($queryBuilder->expr()->like('u.login', ':userLogin'))
//                       //->orWhere($queryBuilder->expr()->andX())
//                       ->setParameter('userLogin', "%$login%");
//
//
//          return $queryBuilder->getQuery()->getResult();
//    }
//
//
//    # DQL (QueryBuilder) use only name properties of Entity
//    public function updateUserLoginWithQueryBuilder(int $userId, string $login): void
//    {
//          $queryBuilder = $this->entityManager->createQueryBuilder();
//
//          $queryBuilder->update(User::class, 'u')
//                       ->set('u.login', ':userLogin')
//                       ->where($queryBuilder->expr()->eq('u.id', ':userId'))
//                       ->setParameter('userId', $userId)
//                       ->setParameter('userLogin', $login);
//
//
//          $queryBuilder->getQuery()->execute();
//    }
//
//
//    # DBAL use names table from database
//    public function updateUserLoginWithDBALQueryBuilder(int $userId, string $login)
//    {
//         $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
//
//         $queryBuilder->update('"user"', 'u')
//                      ->set('login', ':userLogin')
//                      ->where($queryBuilder->expr()->eq('u.id', ':userId'))
//                      ->setParameter('userId', $userId)
//                      ->setParameter('userLogin', $login);
//
//
//         $queryBuilder->executeQuery();
//    }
//
//
//    public function findUserWithTweetsWithQueryBuilder(int $userId): array
//    {
//         $queryBuilder = $this->entityManager->createQueryBuilder();
//
//         $queryBuilder->select('u', 't')
//                      ->from(User::class, 'u')
//                      ->leftJoin('u.tweets', 't')
//                      ->where($queryBuilder->expr()->eq('u.id', ':userId'))
//                      ->setParameter('userId', $userId);
//
//         return $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
//    }
//
//
//    public function findUserWithTweetsWithDBALQueryBuilder(int $userId): array
//    {
//        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
//
//        $queryBuilder->select('u', 't')
//                     ->from('"user"', 'u')
//                     ->leftJoin('u', 'tweet', 't', 'u.id = t.author_id')
//                     ->where($queryBuilder->expr()->eq('u.id', ':userId'))
//                     ->setParameter('userId', $userId);
//
//
//        return $queryBuilder->executeQuery()->fetchAllNumeric();
//
//    }


}