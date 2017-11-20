<?php

namespace Zima\BlogwebBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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
     * @var string
     * @ORM\Column(name="fullname", type="string", length=50)
     */
    private $fullname;

    /**
     * @var \DateTime
     * @ORM\Column(name="birthday", type="datetime")
     */
    private $birthday;

    /**
     * @var string
     * @ORM\Column(name="main_title", type="string", length=255)
     */
    private $maintitles;

    /**
     * @var string
     * @ORM\Column(name="interests", type="string", length=255)
     */
    private $interests;

    /**
     * @var string
     * @ORM\Column(name="about_me", type="text")
     */
    private $aboutme;


    public function __construct()
    {
        parent::__construct();
        $this->posts = new ArrayCollection();
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
    public function getMaintitles()
    {
        return $this->maintitles;
    }

    /**
     * @param string $maintitles
     */
    public function setMaintitles($maintitles)
    {
        $this->maintitles = $maintitles;
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

}