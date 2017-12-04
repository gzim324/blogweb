<?php

namespace Zima\BlogwebBundle\Repository;

use Symfony\Component\HttpFoundation\Request;
use Zima\BlogwebBundle\Entity\User;

class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     * @param User $user
     */
    public function findInfo(User $user) {
        return $this->createQueryBuilder("user")
            ->where("user.id = :user")
            ->setParameter("user", $user->getId())
            ->getQuery()
            ->getResult();
    }


}