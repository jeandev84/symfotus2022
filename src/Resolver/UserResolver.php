<?php
namespace App\Resolver;

#use ApiPlatform\Core\GraphQl\Resolver\QueryCollectionResolverInterface;
use ApiPlatform\GraphQl\Resolver\QueryItemResolverInterface;
use App\Entity\User;
use App\Manager\UserManager;

// Resolver для api platform
class UserResolver implements QueryItemResolverInterface
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager) {
        $this->userManager = $userManager;
    }

    /**
     * @param User|null $item
     */
    public function __invoke($item, array $context): User
    {
        if (isset($context['args']['id'])) {
            $item = $this->userManager->findUserById($context['args']['id']);
        } elseif (isset($context['args']['login'])) {
            $item = $this->userManager->findUserByLogin($context['args']['login']);
        }


        // TODO : call UserMaskResolver()
        // $resolver = new UserMaskResolver();
        // $resolver->__invoke($item, $context);

        return $item;
    }
}