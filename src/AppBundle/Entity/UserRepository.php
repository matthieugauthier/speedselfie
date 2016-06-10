<?php

namespace AppBundle\Entity;

/**
 * UserRepository
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function findMates(User $user)
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->select('u')
            ->where('u.id != :id')
            ->setParameter('id', $user->getId());
        //$qb->setMaxResults(3);
        return $qb
            ->getQuery()
            ->getResult();
    }
}
