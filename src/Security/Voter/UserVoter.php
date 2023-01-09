<?php
namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;



class UserVoter extends Voter
{

    public const DELETE = 'delete';



    protected function supports(string $attribute, $subject)
    {
         return $attribute === self::DELETE && ($subject instanceof User);
    }


    /**
     * @param string $attribute
     * @param $subject
     * @param TokenInterface $token
     * @return bool
    */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
         $user = $token->getUser();

         if (! $user instanceof User) {
              return false;
         }

         // Проверка говорит о том что не удаляю сам себя :)
         /** @var User $subject */
         return $user->getId() !== $subject->getId();
    }
}