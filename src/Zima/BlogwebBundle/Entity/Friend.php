<?php

namespace Zima\BlogwebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="friend")
 */
class Friend
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var User
     *
     * @ORM\ManyToMany(targetEntity="Zima\BlogwebBundle\Entity\User", inversedBy="friends")
     */
    private $friends;

    /**
     * @ORM\ManyToMany(targetEntity="Zima\BlogwebBundle\Entity\User", inversedBy="owners")
     */
    private $owners;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * @param User $friends
     */
    public function setFriends($friends)
    {
        $this->friends = $friends;
    }

    /**
     * @return User
     */
    public function getOwners()
    {
        return $this->owners;
    }

    /**
     * @param User $owners
     */
    public function setOwners($owners)
    {
        $this->owners = $owners;
    }

}
