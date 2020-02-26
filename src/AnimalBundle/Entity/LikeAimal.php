<?php

namespace AnimalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LikeAimal
 *
 * @ORM\Table(name="like_aimal")
 * @ORM\Entity(repositoryClass="AnimalBundle\Repository\LikeAimalRepository")
 */
class LikeAimal
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    private $user;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getAnimal()
    {
        return $this->animal;
    }

    /**
     * @param mixed $animal
     */
    public function setAnimal($animal)
    {
        $this->animal = $animal;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Animal")
     */
    private $animal;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

