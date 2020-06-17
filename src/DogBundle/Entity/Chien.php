<?php

namespace DogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chien
 *
 * @ORM\Table(name="chien")
 * @ORM\Entity(repositoryClass="DogBundle\Repository\ChienRepository")
 */
class Chien
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
     * @ORM\ManyToOne(targetEntity="DogBundle\Entity\Coach")
     */
    private $coach;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="date")
     */
    private $dateDebut;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer")
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="maladie", type="string", length=255)
     */
    private $maladie;

    /**
     * @var string
     *
     * @ORM\Column(name="type_chasse", type="string", length=255)
     */
    private $typeChasse;

    /**
     * @var int
     *
     * @ORM\Column(name="note", type="integer")
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=255)
     */
    private $etat;



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
     * Set nom.
     *
     * @param string $nom
     *
     * @return Chien
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set dateDebut.
     *
     * @param \DateTime $dateDebut
     *
     * @return Chien
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut.
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set age.
     *
     * @param int $age
     *
     * @return Chien
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age.
     *
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set maladie.
     *
     * @param string $maladie
     *
     * @return Chien
     */
    public function setMaladie($maladie)
    {
        $this->maladie = $maladie;

        return $this;
    }

    /**
     * Get maladie.
     *
     * @return string
     */
    public function getMaladie()
    {
        return $this->maladie;
    }

    /**
     * Set typeChasse.
     *
     * @param string $typeChasse
     *
     * @return Chien
     */
    public function setTypeChasse($typeChasse)
    {
        $this->typeChasse = $typeChasse;

        return $this;
    }

    /**
     * Get typeChasse.
     *
     * @return string
     */
    public function getTypeChasse()
    {
        return $this->typeChasse;
    }

    /**
     * Set note.
     *
     * @param int $note
     *
     * @return Chien
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note.
     *
     * @return int
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set etat.
     *
     * @param string $etat
     *
     * @return Chien
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
     * Set race.
     *
     * @param string $race
     *
     * @return Chien
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

    /**
     * Set user.
     *
     * @param \UserBundle\Entity\User|null $user
     *
     * @return Chien
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
     * Set coach.
     *
     * @param \DogBundle\Entity\Coach|null $coach
     *
     * @return Chien
     */
    public function setCoach(\DogBundle\Entity\Coach $coach = null)
    {
        $this->coach = $coach;

        return $this;
    }

    /**
     * Get coach.
     *
     * @return \DogBundle\Entity\Coach|null
     */
    public function getCoach()
    {
        return $this->coach;
    }
}
