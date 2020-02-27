<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\publication;


/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="about", type="string", length=255, nullable=true)
     */
    private $about=null;

    /**
     * @var string
     *
     * @ORM\Column(name="profilepicture", type="string", length=255, nullable=true)
     */
    private $profilepicture=null;

    /**
     * @ORM\OneToMany(targetEntity="publication", mappedBy="user")
     **/
    private $publications;

    /**
     * @ORM\OneToMany(targetEntity="commentaire", mappedBy="user")
     **/
    private $commentaires;


    public function __construct()
    {
        parent::__construct();
        //$this->publications = new ArrayCollection();
    }

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
     * Set profilepicture
     *
     * @param string $profilepicture
     *
     * @return Produit
     */
    public function setProfilepicture($profilepicture)
    {
        $this->profilepicture = $profilepicture;

        return $this;
    }

    /**
     * Get profilepicture
     *
     * @return string
     */
    public function getProfilepicture()
    {
        return $this->profilepicture;
    }

    /**
     * Set about
     *
     * @param string $about
     *
     * @return User
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }
}

