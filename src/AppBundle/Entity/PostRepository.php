<?php

namespace AppBundle\Entity;

/**
 * UserRepository
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function findOpenType($type, $user)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where('p.type = :type')
            ->setParameter('type', $type)
            ->andWhere('p.photo IS NULL')
            ->andWhere('p.author = :user')
            ->setParameter('user', $user);

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findClose($maxResults = 0)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where('p.photo IS NOT NULL')
            ->orderBy('p.datetime','DESC');

        if($maxResults) {
            $qb->setMaxResults($maxResults);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }
}
