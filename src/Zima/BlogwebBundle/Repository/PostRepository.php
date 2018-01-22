<?php

namespace Zima\BlogwebBundle\Repository;

use Symfony\Component\HttpFoundation\Request;
use Zima\BlogwebBundle\Entity\Friends;
use Zima\BlogwebBundle\Entity\Post;
use Zima\BlogwebBundle\Entity\User;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function findUndeletedPost() {
        return $this->createQueryBuilder("post")
            ->where("post.deleted = :false")
            ->setParameter("false", Post::STATUS_DELETED_FALSE)
            ->orderBy("post.createdAt", "DESC")
            ->getQuery()
            ->getResult();
    }


    /**
     * @param Friends $friends
     * @return array
     */
    public function selectFriendsPost(Friends $friends)
    {
        return $this->createQueryBuilder("post")
            ->where("post.owner = :owner")
            ->setParameter("owner", $friends->getFriend())
            ->orderBy("post.createdAt", "DESC")
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     * @return array
     */
    public function findcontents(User $user) {
        return $this->createQueryBuilder("post")
            ->where("post.deleted = :false")
            ->setParameter("false", Post::STATUS_DELETED_FALSE)
            ->andWhere("post.owner = :owner")
            ->setParameter("owner", $user->getId())
            ->orderBy("post.createdAt", "DESC")
            ->getQuery()
            ->getResult();
    }



    /**
     * @param Request $request
     * @return array
     */
    public function searchContents(Request $request) {
        return $this->createQueryBuilder('post')
            ->where('post.tags LIKE :search')
            ->orWhere('post.title LIKE :search')
            ->orWhere('post.shortdescription LIKE :search')
            ->setParameter('search', '%'.$request->get('search').'%')
            ->getQuery()
            ->getResult();
    }

}
