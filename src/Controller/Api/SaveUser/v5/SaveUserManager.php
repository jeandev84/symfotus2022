<?php
namespace App\Controller\Api\SaveUser\v5;

use App\Controller\Api\SaveUser\v5\Output\UserIsSavedDTO;
use App\DTO\SaveUserDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

# По идее можно было назвать SaveUserService
class SaveUserManager
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private UserPasswordHasherInterface $userPasswordHasher
    )
    {

    }


    /**
     * @param SaveUserDTO $saveUserDTO
     * @return UserIsSavedDTO
     * @throws \JsonException
    */
    public function saveUser(SaveUserDTO $saveUserDTO): UserIsSavedDTO
    {
        $user = new User();
        $user->setLogin($saveUserDTO->login);
        $user->setPassword($saveUserDTO->password);
        $user->setRoles($saveUserDTO->roles);
        $user->setAge($saveUserDTO->age);
        $user->setIsActive($saveUserDTO->isActive);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $result = new UserIsSavedDTO();
        $context = (new SerializationContext())->setGroups(['user:write', 'user:read']);
        $result->loadFromJsonString($this->serializer->serialize($user, 'json', $context));

        return $result;
    }
}