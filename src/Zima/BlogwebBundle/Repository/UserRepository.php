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
    public function findInfo(User $user)
    {
        return $this->createQueryBuilder("user")
            ->where("user.id = :user")
            ->setParameter("user", $user->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function selectFriends(User $user)
    {
        return $this->createQueryBuilder('user')
            ->select('user', 'friends', 'owners')
            ->leftJoin('user.friends', 'owners')
            ->leftJoin('user.owners', 'friends')
            ->where('owners.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function searchUsers(Request $request)
    {
        return $this->createQueryBuilder('user')
            ->where('user.fullname LIKE :search')
            ->orwhere('user.username LIKE :search')
            ->setParameter('search', '%'.$request->get('searchUsers').'%')
            ->getQuery()
            ->getResult();
    }
}
