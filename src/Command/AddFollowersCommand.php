<?php
namespace App\Command;

use App\DTO\SaveUserDTO;
use App\Entity\User;
use App\Service\SubscriptionService;
use App\Manager\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AddFollowersCommand extends Command
{
    use LockableTrait;

    public const FOLLOWERS_ADD_COMMAND_NAME = 'followers:add';

    private const DEFAULT_FOLLOWERS = 100;
    private const DEFAULT_LOGIN_PREFIX = 'Reader #';

    private UserManager $userManager;

    private SubscriptionService $subscriptionService;

    public function __construct(UserManager $userManager, SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->userManager = $userManager;
        $this->subscriptionService = $subscriptionService;
    }

    protected function configure(): void
    {
        $this->setName(self::FOLLOWERS_ADD_COMMAND_NAME)
            ->setHidden(true) // это для того чтобы команда не попал в списке, а работать будет дальше
            ->setDescription('Adds followers to author')
            ->addArgument('authorId', InputArgument::REQUIRED, 'ID of author')
            #->addArgument('count', InputArgument::REQUIRED, 'How many followers should be added')
            ->addOption('login', 'l', InputOption::VALUE_REQUIRED, 'Follower login prefix')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Показывает в случае если команда уже запущена через крон.
        // Чтобы запустить несколько процессов в кроне например
        if (! $this->lock()) {
            $output->write("<info>Command is already running.</info>\n");
            return self::SUCCESS;
        }
        sleep(100);

        $authorId = (int)$input->getArgument('authorId');
        $user = $this->userManager->findUserById($authorId);
        if ($user === null) {
            $output->write("<error>User with ID $authorId doesn't exist</error>\n");
            return self::FAILURE;
        }

        # $count = (int)$input->getArgument('count');
        $helper = $this->getHelper('question');
        $question = new Question('How many followers you want to add?', self::DEFAULT_FOLLOWERS);
        $count = (int)$helper->ask($input, $output, $question);

        if ($count < 0) {
            $output->write("<error>Count should be positive integer</error>\n");
            return self::FAILURE;
        }

        # $result = $this->subscriptionService->addFollowers($user, "Reader #$authorId", $count);
        $login = $input->getOption('login') ?? self::DEFAULT_LOGIN_PREFIX;
        $result = $this->subscriptionService->addFollowers($user, $login.$authorId, $count);


        $output->write("<info>$result followers were created</info>\n");

        return self::SUCCESS;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \JsonException
     */
    protected function executeWithProgressBar(InputInterface $input, OutputInterface $output): int
    {
        // Показывает в случае если команда уже запущена через крон.
        // Чтобы запустить несколько процессов в кроне например
        if (! $this->lock()) {
            $output->write("<info>Command is already running.</info>\n");
            return self::SUCCESS;
        }

        $authorId = (int)$input->getArgument('authorId');
        $user = $this->userManager->findUserById($authorId);
        if ($user === null) {
            $output->write("<error>User with ID $authorId doesn't exist</error>\n");
            return self::FAILURE;
        }

        $count = (int)$input->getArgument('count');

        if ($count < 0) {
            $output->write("<error>Count should be positive integer</error>\n");
            return self::FAILURE;
        }

        $loginPrefix = $input->getOption('login') ?? self::DEFAULT_LOGIN_PREFIX;
        $progressBar  = new ProgressBar($output, $count);
        $progressBar->start();
        $createdFollowers = 0;
        for ($i = 0; $i < $count; $i++) {
            $login = "{$loginPrefix}_#$i";
            $password = $login;
            $age = $i;
            $isActive = true;
            $phone = '+'.str_pad((string)abs(crc32($login)), 10, '0');
            $email = "$login@gmail.com";
            $preferred = random_int(0, 1) === 1 ? User::EMAIL_NOTIFICATION : User::SMS_NOTIFICATION;
            $data = compact('login', 'password', 'age', 'isActive', 'phone', 'email', 'preferred');
            $followerId = $this->userManager->saveUserFromDTO(new User(), new SaveUserDTO($data));
            if ($followerId !== null) {
                $this->subscriptionService->subscribe($user->getId(), $followerId);
                $createdFollowers++;
                usleep(100000);
                $progressBar->advance();
            }
        }


        $output->write("<info>$createdFollowers followers were created</info>\n");
        $progressBar->finish();

        return self::SUCCESS;
    }
}