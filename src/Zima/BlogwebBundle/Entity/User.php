<?php

namespace Zima\BlogwebBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Zima\BlogwebBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Post[]ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Post", mappedBy="owner")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $posts;

    /**
     * @var Comments[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Comments", mappedBy="owner")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $comment;

    /**
     * @var string
     * @ORM\Column(name="fullname", type="string", length=50, nullable=true)
     */
    private $fullname;

    /**
     * @var \DateTime
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     * @ORM\Column(name="interests", type="string", length=255, nullable=true)
     */
    private $interests;

    /**
     * @var string
     * @ORM\Column(name="about_me", type="text", nullable=true)
     */
    private $aboutme;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="User", inversedBy="friends")
     * @ORM\JoinTable(name="Friend",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
     *      )
     */
    private $owners;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="User", mappedBy="owners")
     */
    private $friends;

    public function __construct()
    {
        parent::__construct();
        $this->posts = new ArrayCollection();
        $this->comment = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->owners = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Post[]|ArrayCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param Post $posts
     * @return $this
     */
    public function addPosts(Post $posts)
    {
        $this->posts[] = $posts;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getAboutme()
    {
        return $this->aboutme;
    }

    /**
     * @param string $aboutme
     */
    public function setAboutme($aboutme)
    {
        $this->aboutme = $aboutme;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param string $fullname
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    /**
     * @return string
     */
    public function getInterests()
    {
        return $this->interests;
    }

    /**
     * @param string $interests
     */
    public function setInterests($interests)
    {
        $this->interests = $interests;
    }

    /**
     * @return ArrayCollection|Comments[]
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param ArrayCollection|Comments[] $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return ArrayCollection
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * @param ArrayCollection $friends
     */
    public function setFriends($friends)
    {
        $this->friends = $friends;
    }

    /**
     * @return ArrayCollection
     */
    public function getOwners()
    {
        return $this->owners;
    }

    /**
     * @param ArrayCollection $owners
     */
    public function setOwners($owners)
    {
        $this->owners = $owners;
    }



}
