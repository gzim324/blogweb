<?php

namespace Zima\BlogwebBundle\Repository;

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


    /**
     * @param User $user
     * @return mixed
     */
    public function selectFriends(User $user)
    {
        return $this->createQueryBuilder('user')
            ->select('user', 'friends', 'owners')
            ->leftJoin('user.friends', 'friends')
            ->leftJoin('user.owners', 'owners')
            ->where('friends.id = :userId')     //tak wiem, musze to zamienić…
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @param User $user
//     * @return array
//     */
//    public function selectFriends(User $user) {
//        return $this->createQueryBuilder('user')
//            ->select('user', 'friends', 'owners')
//            ->leftJoin('user.friends', 'friends')
//            ->leftJoin('user.owners', 'owners')
//            ->where('user.owners = :userId')
//            ->setParameter('userId', $user->getId())
//            ->getQuery()
//            ->getResult();
//    }

}