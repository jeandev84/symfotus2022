<?php
namespace App\Resolver;

#use ApiPlatform\Core\GraphQl\Resolver\QueryCollectionResolverInterface;
use ApiPlatform\GraphQl\Resolver\QueryItemResolverInterface;
use App\Entity\User;

// Resolver для api platform
class UserMaskResolver implements QueryItemResolverInterface
{
    private const MASK = '****';



    /**
     * @param User|null $item
    */
    public function __invoke($item, array $context): User
    {
        if ($item->isProtected()) {
            $item->setLogin(self::MASK);
            $item->setPassword(self::MASK);
        }

        return $item;
    }
}