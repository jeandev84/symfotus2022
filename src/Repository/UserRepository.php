<?php
namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

      /**
       * @param int $page
       * @param int $perPage
       * @return User[]
      */
      public function getUsers(int $page, int $perPage): array
      {
           $qb = $this->getEntityManager()->createQueryBuilder();

           $qb->select('u')
              ->from($this->getClassName(), 'u')
              ->orderBy('u.id', 'DESC')
              ->setFirstResult($perPage * $page)
              ->setMaxResults($perPage);


           return $qb->getQuery()->getResult();
      }
}