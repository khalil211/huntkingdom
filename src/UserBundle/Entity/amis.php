<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * amis
 *
 * @ORM\Table(name="amis")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\amisRepository")
 */
class amis
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
     * @var int
     *
     * @ORM\Column(name="firstuser", type="integer")
     */
    private $firstuser;

    /**
     * @var int
     *
     * @ORM\Column(name="seconduser", type="integer")
     */
    private $seconduser;

    /**
     * @var int
     *
     * @ORM\Column(name="etat", type="integer")
     */
    private $etat;

    /**
     * @var int
     *
     * @ORM\Column(name="actionuser", type="integer")
     */
    private $actionuser;


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
     * Set firstuser
     *
     * @param integer $firstuser
     *
     * @return amis
     */
    public function setFirstuser($firstuser)
    {
        $this->firstuser = $firstuser;

        return $this;
    }

    /**
     * Get firstuser
     *
     * @return int
     */
    public function getFirstuser()
    {
        return $this->firstuser;
    }

    /**
     * Set seconduser
     *
     * @param integer $seconduser
     *
     * @return amis
     */
    public function setSeconduser($seconduser)
    {
        $this->seconduser = $seconduser;

        return $this;
    }

    /**
     * Get seconduser
     *
     * @return int
     */
    public function getSeconduser()
    {
        return $this->seconduser;
    }

    /**
     * Set etat
     *
     * @param integer $etat
     *
     * @return amis
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return int
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set actionuser
     *
     * @param integer $etat
     *
     * @return amis
     */
    public function setActionuser($actionuser)
    {
        $this->actionuser = $actionuser;

        return $this;
    }

    /**
     * Get actionuser
     *
     * @return int
     */
    public function getActionuser()
    {
        return $this->actionuser;
    }


}

