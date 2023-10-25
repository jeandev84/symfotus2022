<?php
namespace App\Controller\Api\SaveUser\v5;

use App\Client\StatsdAPIClient;
use App\Controller\Api\SaveUser\v5\Output\UserIsSavedDTO;
use App\Controller\Api\SaveUser\v5\Input\SaveUserDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

# По идее можно было назвать SaveUserService
class SaveUserManager
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private UserPasswordHasherInterface $userPasswordHasher,
        private LoggerInterface $logger,
        private StatsdAPIClient $statsdAPIClient
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

        // Graphite client
        $this->statsdAPIClient->increment('save_user_v5_attempt');

        // Monolog
        $this->log();

        $user = new User();
        $user->setLogin($saveUserDTO->login);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $saveUserDTO->password));
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



    public function log()
    {
         $this->logger->debug('This is debug message');
         $this->logger->info('This is info message');
         $this->logger->notice('This is notice message');
         $this->logger->warning('This is warning message');
         $this->logger->error('This is error message');
         $this->logger->critical('This is critical message');
         $this->logger->alert('This is alert message');
         $this->logger->emergency('This is emergency message');
    }
}