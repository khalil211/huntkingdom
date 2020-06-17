<?php

namespace DogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Coach
 *
 * @ORM\Table(name="coach")
 * @ORM\Entity(repositoryClass="DogBundle\Repository\CoachRepository")
 */
class Coach
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
     * @var int
     *
     * @ORM\Column(name="experience", type="integer")
     */
    private $experienceYears;
    /**
     * @var int
     *
     */
    private $nbr;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=255)
     */
    private $etat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hire_date", type="date")
     */
    private $hireDate;

    /**
     * @var string
     *
     * @ORM\Column(name="race", type="string", length=255)
     */
    private $race;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user.
     *
     * @param \UserBundle\Entity\User|null $user
     *
     * @return Coach
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \UserBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set experienceYears.
     *
     * @param int $experienceYears
     *
     * @return Coach
     */
    public function setExperienceYears($experienceYears)
    {
        $this->experienceYears = $experienceYears;

        return $this;
    }

    /**
     * Get experienceYears.
     *
     * @return int
     */
    public function getExperienceYears()
    {
        return $this->experienceYears;
    }

    /**
     * Set etat.
     *
     * @param string $etat
     *
     * @return Coach
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat.
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }
    /**
     * Set nbr.
     *
     * @param string $nbr
     *
     * @return Coach
     */
    public function setNbr($nbr)
    {
        $this->nbr = $nbr;

        return $this;
    }

    /**
     * Get etat.
     *
     * @return string
     */
    public function getNbr()
    {
        return $this->nbr;
    }

    /**
     * Set hireDate.
     *
     * @param \DateTime $hireDate
     *
     * @return Coach
     */
    public function setHireDate($hireDate)
    {
        $this->hireDate = $hireDate;

        return $this;
    }

    /**
     * Get hireDate.
     *
     * @return \DateTime
     */
    public function getHireDate()
    {
        return $this->hireDate;
    }

    /**
     * Set race.
     *
     * @param string $race
     *
     * @return Coach
     */
    public function setRace($race)
    {
        $this->race = $race;

        return $this;
    }

    /**
     * Get race.
     *
     * @return string
     */
    public function getRace()
    {
        return $this->race;
    }
}
