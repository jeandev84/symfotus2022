<?php
namespace App\Resolver;

#use ApiPlatform\Core\GraphQl\Resolver\QueryCollectionResolverInterface;
use ApiPlatform\GraphQl\Resolver\QueryCollectionResolverInterface;
use App\Entity\User;


// Resolver для api platform
class UserCollectionResolver implements QueryCollectionResolverInterface
{
    private const MASK = '****';

    /**
     * @param iterable<User> $collection
     * @param array $context
     *
     * @return iterable<User>
     */
    public function __invoke(iterable $collection, array $context): iterable
    {
        /** @var User $user */
        foreach ($collection as $user) {
            if ($user->isProtected()) {
                $user->setLogin(self::MASK);
                $user->setPassword(self::MASK);
            }
        }

        return $collection;
    }
}